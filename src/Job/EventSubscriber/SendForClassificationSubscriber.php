<?php

namespace App\Job\EventSubscriber;

use App\Job\Event\JobPostedEvent;
use App\Message\Job\ClassifyMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class SendForClassificationSubscriber implements EventSubscriberInterface
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

        if ($job->isManualPublishing()) {
            return;
        }

        if (empty($job->getDescription())) {
            return;
        }

        $this->bus->dispatch(new ClassifyMessage($job->getId()));
    }
}
