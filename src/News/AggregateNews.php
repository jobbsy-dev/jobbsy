<?php

namespace App\News;

use App\Repository\FeedRepository;

final class AggregateNews
{
    public function __construct(
        private readonly FeedRepository $feedRepository,
        private readonly FetchArticlesFromFeed $fetchArticlesFromFeed
    ) {
    }

    public function __invoke(): array
    {
        $feeds = $this->feedRepository->findAll();

        $articles = [];
        foreach ($feeds as $feed) {
            $articles[] = ($this->fetchArticlesFromFeed)($feed);
        }

        return array_merge(...$articles);
    }
}
