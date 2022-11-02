<?php

namespace App\Tests\NewsAggregator\Source;

use App\NewsAggregator\RSS\Client;
use App\NewsAggregator\Source\SymfonyProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class SymfonyProviderTest extends TestCase
{
    public function testRetrieve(): void
    {
        // Arrange
        $expectedData = file_get_contents(__DIR__.'/../RSS/data/symfony_blog.xml');
        $mockResponse = new MockResponse($expectedData);
        $mockHttpClient = new MockHttpClient($mockResponse);
        $rssClient = new Client($mockHttpClient);
        $provider = new SymfonyProvider($rssClient);

        // Act
        $articles = $provider->retrieve();

        // Assert
        self::assertCount(4, $articles);
        self::assertSame('A Week of Symfony #826 (24-30 October 2022)', $articles[0]->getTitle());
    }
}
