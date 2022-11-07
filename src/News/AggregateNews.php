<?php

namespace App\News;

use App\Repository\FeedRepository;
use Psr\Log\LoggerInterface;

final class AggregateNews
{
    public function __construct(
        private readonly FeedRepository $feedRepository,
        private readonly FetchArticlesFromFeed $fetchArticlesFromFeed,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(): array
    {
        $feeds = $this->feedRepository->findAll();

        $articles = [];
        foreach ($feeds as $feed) {
            try {
                $articles[] = ($this->fetchArticlesFromFeed)($feed);
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        }

        return array_merge(...$articles);
    }
}
