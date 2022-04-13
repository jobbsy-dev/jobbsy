<?php

namespace App\Tests\Subscription;

use App\Subscription\SubscriptionMailingListInterface;

class NullSubscriptionAdapter implements SubscriptionMailingListInterface
{
    public function subscribe(string $email, string $mailingList): void
    {
    }
}
