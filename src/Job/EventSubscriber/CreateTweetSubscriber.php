<?php

namespace App\Job\EventSubscriber;

use App\Job\Event\JobPostedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CreateTweetSubscriber implements EventSubscriberInterface
{
    public function __construct(private MessageBusInterface $bus)
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

//        $this->bus->dispatch(new CreateTweetMessage($job->getId(), $event->jobUrl));
    }
}
