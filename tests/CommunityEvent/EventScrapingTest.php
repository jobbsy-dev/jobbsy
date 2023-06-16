<?php

namespace App\Tests\CommunityEvent;

use App\CommunityEvent\EventScraping;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class EventScrapingTest extends TestCase
{
    public function test_crawl(): void
    {
        // Arrange
        $mockResponse = new MockResponse(file_get_contents(__DIR__.'/fixtures/meetup_events_page.html'));
        $mockClient = new MockHttpClient([$mockResponse]);
        $goutteClient = new HttpBrowser($mockClient);
        $meetupCrawler = new EventScraping($goutteClient);

        // Act
        $data = $meetupCrawler->fetch('https://www.meetup.com/backendos/events');

        // Assert
        self::assertCount(1, $data);
        $meetupData = $data[0];
        self::assertSame('Backend User Group #21', $meetupData['name']);
        self::assertSame('https://www.meetup.com/backendos/events/290348177/', $meetupData['url']);
        self::assertSame('2023-01-19T18:30+01:00', $meetupData['startDate']);
    }
}
