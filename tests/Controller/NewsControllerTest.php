<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class NewsControllerTest extends WebTestCase
{
    public function test_index(): void
    {
        $client = self::createClient();

        $crawler = $client->request('GET', '/news');

        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('News');

        self::assertSelectorTextContains('h1', 'News');
        self::assertCount(2, $crawler->filter('article'));
    }
}
