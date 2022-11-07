<?php

namespace App\Tests\Controller;

use StellaMaris\Clock\ClockInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();

        $clock = static::getContainer()->get(ClockInterface::class);
        $clock->setNow(\DateTimeImmutable::createFromFormat('Y-m-d', '2022-06-10'));

        $client->request('GET', '/events');

        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('Symfony conferences and events');

        self::assertSelectorTextContains('h1', 'Upcoming Symfony conferences and events');
        self::assertSelectorTextContains('h2', 'Upcoming events & meetups');
    }
}
