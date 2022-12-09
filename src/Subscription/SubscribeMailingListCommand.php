<?php

namespace App\Subscription;

final readonly class SubscribeMailingListCommand
{
    public function __construct(
        public string $email,
        public string $listId
    ) {
    }
}
