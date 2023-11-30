<?php

namespace App\Controller;

use App\Entity\CommunityEvent\Event;
use App\Repository\CommunityEvent\EventRepository;
use League\Uri\Modifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;

final class EventController extends AbstractController
{
    public function __construct(private readonly EventRepository $eventRepository)
    {
    }

    #[Route('/events', name: 'event_index', methods: ['GET'])]
    #[Cache(smaxage: 86400)]
    public function index(): Response
    {
        return $this->render('event/index.html.twig', [
            'upcomingEvents' => $this->eventRepository->findUpcomingEvents(),
            'pastEvents' => $this->eventRepository->findPastEvents(),
        ]);
    }

    #[Route('/events/rss.xml', name: 'event_rss', defaults: ['_format' => 'xml'], methods: ['GET']), ]
    public function rss(): Response
    {
        return $this->render('event/index.xml.twig', [
            'events' => $this->eventRepository->findBy([], ['startDate' => 'DESC'], 30),
        ]);
    }

    #[Route('/events/{id}', name: 'event_redirect', methods: ['GET'])]
    public function event(Event $event): RedirectResponse
    {
        /** @var string $eventUrl */
        $eventUrl = $event->getUrl();
        $uri = Modifier::from($eventUrl)->appendQuery('ref=jobbsy');

        return $this->redirect($uri);
    }
}
