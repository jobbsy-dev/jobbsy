<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DefaultControllerTest extends WebTestCase
{
    /**
     * @dataProvider provideUrls
     */
    public function testUrlIsOk(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        self::assertResponseIsSuccessful();
    }

    public static function provideUrls(): \Generator
    {
        yield ['/symfony-location-remote-jobs'];
        yield ['/symfony-location-onsite-jobs'];
        yield ['/symfony-location-hybrid-jobs'];
        yield ['/symfony-employment-fulltime-jobs'];
        yield ['/symfony-employment-contract-jobs'];
        yield ['/symfony-employment-internship-jobs'];
        yield ['/rss.xml'];
        yield ['/events/rss.xml'];
        yield ['/news/rss.xml'];
    }
}
