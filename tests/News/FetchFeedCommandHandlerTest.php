<?php

declare(strict_types=1);

namespace App\Tests\News;

use App\Entity\News\Entry;
use App\Entity\News\Feed;
use App\News\Aggregator\FetchArticlesFromFeed;
use App\News\FetchFeedCommand;
use App\News\FetchFeedCommandHandler;
use App\Tests\News\Aggregator\InMemoryFetchArticlesFromFeed;
use App\Tests\Repository\InMemoryEntryRepository;
use App\Tests\Repository\InMemoryFeedRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Ramsey\Uuid\Uuid;

final class FetchFeedCommandHandlerTest extends TestCase
{
    public function test_fetch(): void
    {
        // Arrange
        $feed = new Feed(Uuid::fromString('305a2ac5-0615-46f5-91b7-36d5c43e4ef0'));
        $feedRepository = new InMemoryFeedRepository([$feed]);
        $entry = new Entry();
        $entry->setTitle('Title 1');
        $entry->setLink('https://example.com');

        $fetchArticlesFromFeed = new InMemoryFetchArticlesFromFeed([$entry]);
        $fetchArticlesFromFeedMain = new FetchArticlesFromFeed([$fetchArticlesFromFeed], new NullLogger());
        $entryRepository = new InMemoryEntryRepository();

        $handler = new FetchFeedCommandHandler(
            $feedRepository,
            $fetchArticlesFromFeedMain,
            $entryRepository,
            new NullLogger(),
            $this->createMock(EntityManagerInterface::class),
        );

        // Act
        ($handler)(new FetchFeedCommand('305a2ac5-0615-46f5-91b7-36d5c43e4ef0'));

        // Assert
        self::assertCount(1, $entryRepository->getAll());
        /** @var Entry $entry */
        $entry = current($entryRepository->getAll());
        self::assertSame('Title 1', $entry->getTitle());
        self::assertSame('https://example.com', $entry->getLink());
    }
}
