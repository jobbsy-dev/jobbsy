<?php

namespace App\Tests\News\Aggregator\Atom;

use App\News\Aggregator\Atom\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ClientTest extends TestCase
{
    public function testReadFeed(): void
    {
        // Arrange
        $xmlResponse = file_get_contents(__DIR__.'/data/feed.xml');
        $mockResponse = new MockResponse($xmlResponse);
        $mockHttpClient = new MockHttpClient($mockResponse);
        $rssClient = new Client($mockHttpClient);

        // Act
        $feed = $rssClient->get('http://localhost');

        // Assert
        self::assertNotNull($feed);
        self::assertSame('The Strangebuzz PHP/Symfony blog.', $feed->title);
        self::assertCount(4, $feed->getEntries());
        $entry = $feed->getEntries()[0];
        self::assertSame('Validating your data fixtures with the Alice Symfony bundle', trim($entry->title));
    }
}
