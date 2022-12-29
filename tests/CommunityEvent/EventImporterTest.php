<?php

namespace App\Tests\CommunityEvent;

use App\CommunityEvent\EventImporter;
use App\CommunityEvent\EventScraping;
use App\Entity\CommunityEvent\Source;
use App\Tests\Repository\CommunityEvent\InMemorySourceRepository;
use Goutte\Client;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class EventImporterTest extends TestCase
{
    public function testImport(): void
    {
        // Arrange
        $mockResponse = new MockResponse(file_get_contents(__DIR__.'/fixtures/meetup_events_page.html'));
        $mockClient = new MockHttpClient([$mockResponse]);
        $goutteClient = new Client($mockClient);

        $source1 = new Source();
        $source1->setUrl('http://localhost/1');
        $source2 = new Source();
        $source2->setUrl('http://localhost/2');
        $repository = new InMemorySourceRepository([$source1, $source2]);

        $scraper = new EventScraping($goutteClient);
        $importer = new EventImporter(
            $repository,
            new NullLogger(),
            $scraper,
        );

        // Act
        $events = $importer->import();

        // Assert
        self::assertCount(1, $events);
        $meetup = $events[0];
        self::assertSame('Backend User Group #21', $meetup->getName());
        self::assertSame('https://www.meetup.com/backendos/events/290348177/', $meetup->getUrl());
        self::assertSame('2023-01-19T18:30+01:00', $meetup->getStartDate()->format('Y-m-d\TH:iP'));
    }
}
