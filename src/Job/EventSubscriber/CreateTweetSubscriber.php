<?php

namespace App\Job\EventSubscriber;

use App\Job\Event\JobPostedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class CreateTweetSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            JobPostedEvent::class => 'onJobPosted',
        ];
    }

    public function onJobPosted(JobPostedEvent $event): void
    {
        //        $this->bus->dispatch(new CreateTweetMessage($job->getId(), $event->jobUrl));
    }
}
