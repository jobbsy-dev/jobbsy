<?php

namespace App\Tests\CommunityEvent\Meetup;

use App\CommunityEvent\Meetup\MeetupCrawler;
use Goutte\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class MeetupCrawlerTest extends TestCase
{
    public function testCrawl(): void
    {
        // Arrange
        $mockResponse = new MockResponse(file_get_contents(__DIR__.'/fixtures/meetup_events_page.html'));
        $mockClient = new MockHttpClient([$mockResponse]);
        $goutteClient = new Client($mockClient);
        $meetupCrawler = new MeetupCrawler($goutteClient);

        // Act
        $data = $meetupCrawler->crawl('http://localhost');

        // Assert
        self::assertCount(1, $data);
        $meetupData = $data[0];
        self::assertSame('Backend User Group #21', $meetupData['name']);
        self::assertSame('https://www.meetup.com/backendos/events/290348177/', $meetupData['url']);
        self::assertSame('2023-01-19T18:30+01:00', $meetupData['startDate']);
    }
}
