<?php

declare(strict_types=1);

namespace App\Tests\News\Aggregator;

use App\Entity\News\Entry;
use App\Entity\News\Feed;
use App\News\Aggregator\FetchArticlesFromFeedInterface;

final readonly class InMemoryFetchArticlesFromFeed implements FetchArticlesFromFeedInterface
{
    /**
     * @param Entry[] $entries
     */
    public function __construct(private array $entries)
    {
    }

    public function __invoke(Feed $feed): array
    {
        return $this->entries;
    }

    public function supports(Feed $feed): bool
    {
        return true;
    }
}
