<?php

namespace App\Subscription;

class SubscribeMailingListCommandHandler
{
    public function __construct(private readonly SubscriptionMailingListInterface $subscriptionMailingList)
    {
    }

    public function __invoke(SubscribeMailingListCommand $command): void
    {
        $this->subscriptionMailingList->subscribe($command->email, $command->listId);
    }
}
