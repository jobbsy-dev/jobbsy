<?php

namespace App\Subscription;

class SubscribeMailingListCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $listId
    ) {
    }
}
