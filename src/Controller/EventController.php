<?php

namespace App\Controller;

use App\Analytics\AnalyticsClient;
use App\Analytics\Plausible\EventRequest;
use App\Entity\CommunityEvent\Event;
use App\Repository\CommunityEvent\EventRepository;
use League\Uri\Uri;
use League\Uri\UriModifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class EventController extends AbstractController
{
    public function __construct(private readonly AnalyticsClient $client)
    {
    }

    #[Route('/events', name: 'event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'upcomingEvents' => $eventRepository->findUpcomingEvents(),
            'pastEvents' => $eventRepository->findPastEvents(),
        ]);
    }

    #[Route('/events/{id}', name: 'event_redirect', methods: ['GET'])]
    public function event(Request $request, Event $event): RedirectResponse
    {
        $this->client->event(EventRequest::create([
            'User-Agent' => $request->headers->get('User-Agent'),
            'X-Forwarded-For' => implode(',', $request->getClientIps()),
            'domain' => 'jobbsy.dev',
            'name' => 'pageview',
            'url' => $request->getUri(),
        ]));

        $uri = Uri::createFromString($event->getUrl());
        $uri = UriModifier::appendQuery($uri, 'ref=jobbsy');

        return $this->redirect($uri);
    }
}
