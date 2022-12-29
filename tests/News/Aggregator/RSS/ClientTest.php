<?php

namespace App\Tests\News\Aggregator\RSS;

use App\News\Aggregator\RSS\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class ClientTest extends TestCase
{
    public function testReadRssDocument(): void
    {
        // Arrange
        $xmlResponse = file_get_contents(__DIR__.'/data/symfony_blog.xml');
        $mockResponse = new MockResponse($xmlResponse);
        $mockHttpClient = new MockHttpClient($mockResponse);
        $rssClient = new Client($mockHttpClient);

        // Act
        $document = $rssClient->get('http://localhost');

        // Assert
        self::assertNotNull($document);
        self::assertCount(1, $document->getChannels());
        $channel = $document->getChannels()[0];
        self::assertSame('Symfony Blog', $channel->title);
        self::assertCount(4, $channel->getItems());
        $item = $channel->getItems()[0];
        self::assertSame('A Week of Symfony #826 (24-30 October 2022)', trim((string) $item->title));
    }

    public function testReadRssDocumentExtraProperties(): void
    {
        // Arrange
        $xmlResponse = file_get_contents(__DIR__.'/data/symfony_blog.xml');
        $mockResponse = new MockResponse($xmlResponse);
        $mockHttpClient = new MockHttpClient($mockResponse);
        $rssClient = new Client($mockHttpClient);

        // Act
        $document = $rssClient->get('http://localhost');

        // Assert
        self::assertNotNull($document);
        self::assertCount(1, $document->getChannels());
        $channel = $document->getChannels()[0];
        $item = $channel->getItems()[0];
        self::assertSame('Javier Eguiluz', trim((string) $item->author));
        self::assertSame('2022-10-30', $item->pubDate->format('Y-m-d'));
    }
}
