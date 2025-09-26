<?php

declare(strict_types=1);

namespace App\Tests\CommunityEvent;

use App\CommunityEvent\EventScraping;
use App\CommunityEvent\FetchSourceCommand;
use App\CommunityEvent\FetchSourceCommandHandler;
use App\Entity\CommunityEvent\Source;
use App\Tests\Repository\InMemoryEventRepository;
use App\Tests\Repository\InMemorySourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Ramsey\Uuid\Uuid;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class FetchSourceCommandHandlerTest extends TestCase
{
    public function test_import(): void
    {
        // Arrange
        /** @var string $body */
        $body = file_get_contents(__DIR__.'/fixtures/meetup_events_page.html');
        $mockResponse = new MockResponse($body);
        $mockClient = new MockHttpClient([$mockResponse]);
        $goutteClient = new HttpBrowser($mockClient);

        $source1 = new Source(Uuid::fromString('9a6db56c-97bd-49d6-85af-595d5a172a89'));
        $source1->setUrl('https://www.meetup.com/backendos');

        $repository = new InMemorySourceRepository([$source1]);
        $eventRepository = new InMemoryEventRepository();

        $scraper = new EventScraping($goutteClient);
        $handler = new FetchSourceCommandHandler(
            $repository,
            new NullLogger(),
            $scraper,
            $eventRepository,
            $this->createMock(EntityManagerInterface::class),
        );

        // Act
        ($handler)(new FetchSourceCommand('9a6db56c-97bd-49d6-85af-595d5a172a89'));

        // Assert
        $events = $eventRepository->getAll();
        self::assertCount(1, $events);
        $meetup = current($events);
        self::assertSame('Backend User Group #21', $meetup->getName());
        self::assertSame('https://www.meetup.com/backendos/events/290348177/', $meetup->getUrl());
        self::assertSame('2023-01-19T18:30+01:00', $meetup->getStartDate()->format('Y-m-d\TH:iP'));
    }
}
