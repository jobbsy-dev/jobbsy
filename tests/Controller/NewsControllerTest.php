<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewsControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/news');

        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('News');

        self::assertSelectorTextContains('h1', 'News');
        self::assertCount(2, $crawler->filter('article'));
    }
}
