<?php

namespace App\Subscription;

final readonly class SubscribeMailingListCommandHandler
{
    public function __construct(private SubscriptionMailingListInterface $subscriptionMailingList)
    {
    }

    public function __invoke(SubscribeMailingListCommand $command): void
    {
        $this->subscriptionMailingList->subscribe($command->email, $command->listId);
    }
}
