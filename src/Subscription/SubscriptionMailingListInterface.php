<?php

namespace App\Subscription;

interface SubscriptionMailingListInterface
{
    public function subscribe(string $email, string $mailingList): void;
}
