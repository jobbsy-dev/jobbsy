<?php

namespace App\EventSubscriber;

use App\Event\JobPostedEvent;
use App\Message\CreateTweetMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateTweetSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            JobPostedEvent::class => 'onJobPosted',
        ];
    }

    public function onJobPosted(JobPostedEvent $event): void
    {
        $job = $event->job;

        $text = sprintf(
            '💻 %s \n 🎸 %s \n 👇 \n %s',
            $job->getTitle(),
            $job->getOrganization(),
            $event->jobUrl,
        );

        $this->bus->dispatch(new CreateTweetMessage($text));
    }
}
