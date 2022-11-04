<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use League\Uri\Uri;
use League\Uri\UriModifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    #[Route('/events', name: 'event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'upcomingEvents' => $eventRepository->findUpcomingEvents(),
            'pastEvents' => $eventRepository->findPastEvents(),
        ]);
    }

    #[Route('/events/{id}', name: 'event_redirect', methods: ['GET'])]
    public function event(Event $event): RedirectResponse
    {
        $uri = Uri::createFromString($event->getUrl());
        $uri = UriModifier::appendQuery($uri, 'ref=jobbsy');

        return $this->redirect($uri);
    }
}
