<?php

declare(strict_types=1);

namespace App\Tests\News\Aggregator;

use App\Entity\News\Entry;
use App\Entity\News\Feed;
use App\News\Aggregator\FetchArticlesFromFeedInterface;

final class InMemoryFetchArticlesFromFeed implements FetchArticlesFromFeedInterface
{
    /**
     * @var Entry[]
     */
    private array $entries;

    public function __construct(array $entries)
    {
        $this->entries = $entries;
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
