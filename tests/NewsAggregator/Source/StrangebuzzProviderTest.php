<?php

namespace App\Tests\NewsAggregator\Source;

use App\NewsAggregator\Atom\Client;
use App\NewsAggregator\Source\StrangebuzzProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class StrangebuzzProviderTest extends TestCase
{
    public function testRetrieve(): void
    {
        // Arrange
        $expectedData = file_get_contents(__DIR__.'/../Atom/data/feed.xml');
        $mockResponse = new MockResponse($expectedData);
        $mockHttpClient = new MockHttpClient($mockResponse);
        $atomClient = new Client($mockHttpClient);
        $provider = new StrangebuzzProvider($atomClient);

        // Act
        $articles = $provider->retrieve();

        // Assert
        self::assertCount(4, $articles);
        self::assertSame('Validating your data fixtures with the Alice Symfony bundle', $articles[0]->getTitle());
    }
}
