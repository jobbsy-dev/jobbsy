<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Clock\MockClock;

class EventControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();

        /** @var MockClock $clock */
        $clock = static::getContainer()->get(ClockInterface::class);
        $clock->modify('2022-06-10');

        $client->request('GET', '/events');

        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('Symfony conferences and events');

        self::assertSelectorTextContains('h1', 'Upcoming Symfony conferences and events');
        self::assertSelectorTextContains('h2', 'Upcoming events & meetups');
    }
}
