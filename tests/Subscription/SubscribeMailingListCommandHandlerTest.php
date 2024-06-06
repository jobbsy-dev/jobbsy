<?php

namespace App\Tests\Subscription;

use App\Subscription\SubscribeMailingListCommand;
use App\Subscription\SubscribeMailingListCommandHandler;
use PHPUnit\Framework\TestCase;

final class SubscribeMailingListCommandHandlerTest extends TestCase
{
    public function test_subscribe(): void
    {
        $this->expectNotToPerformAssertions();

        $subscriptionAdapter = new NullSubscriptionAdapter();
        $commandHandler = new SubscribeMailingListCommandHandler($subscriptionAdapter);

        $command = new SubscribeMailingListCommand('john@example.com', 1234);

        ($commandHandler)($command);
    }
}
