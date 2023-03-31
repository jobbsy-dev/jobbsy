<?php

namespace App\News\Aggregator;

use App\Repository\News\FeedRepository;
use Psr\Log\LoggerInterface;

final readonly class AggregateNews
{
    public function __construct(
        private FeedRepository $feedRepository,
        private FetchArticlesFromFeed $fetchArticlesFromFeed,
        private LoggerInterface $logger
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
                $this->logger->error($exception->getMessage(), [
                    'feedName' => $feed->getName(),
                    'feedUrl' => $feed->getUrl(),
                ]);
            }
        }

        return array_merge(...$articles);
    }
}
