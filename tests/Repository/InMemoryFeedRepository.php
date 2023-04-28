<?php

namespace App\Tests\Repository;

use App\Entity\News\Feed;
use App\News\FeedRepositoryInterface;

final class InMemoryFeedRepository implements FeedRepositoryInterface
{
    /**
     * @var array<string, Feed>
     */
    private array $feeds = [];

    /**
     * @param Feed[] $feeds
     */
    public function __construct(array $feeds = [])
    {
        foreach ($feeds as $feed) {
            $this->feeds[(string) $feed->getId()] = $feed;
        }
    }

    public function save(Feed $feed): void
    {
        // TODO: Implement save() method.
    }

    public function remove(Feed $feed): void
    {
        // TODO: Implement remove() method.
    }

    public function get(string $id): ?Feed
    {
        return $this->feeds[$id] ?? null;
    }
}
