<?php

namespace App\Subscription;

interface SubscriptionMailingListInterface
{
    public function subscribe(string $email, mixed $mailingList): void;
}
