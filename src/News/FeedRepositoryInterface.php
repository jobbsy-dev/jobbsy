<?php

declare(strict_types=1);

namespace App\News;

use App\Entity\News\Feed;

interface FeedRepositoryInterface
{
    public function save(Feed $feed): void;

    public function remove(Feed $feed): void;

    public function get(string $id): ?Feed;
}
