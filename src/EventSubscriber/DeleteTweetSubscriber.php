<?php

namespace App\EventSubscriber;

use App\Broadcast\Twitter\TwitterApi;
use App\Entity\Job;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class DeleteTweetSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TwitterApi $twitterApi)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityDeletedEvent::class => 'deleteTweet',
        ];
    }

    public function deleteTweet(AfterEntityDeletedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!$entity instanceof Job) {
            return;
        }

        if (null === ($tweetId = $entity->getTweetId())) {
            return;
        }

        $this->twitterApi->deleteTweet($tweetId);
    }
}
