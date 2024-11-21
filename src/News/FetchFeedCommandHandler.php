<?php

declare(strict_types=1);

namespace App\News;

use App\News\Aggregator\FetchArticlesFromFeed;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class FetchFeedCommandHandler
{
    public function __construct(
        private FeedRepositoryInterface $feedRepository,
        private FetchArticlesFromFeed $fetchArticlesFromFeed,
        private EntryRepositoryInterface $entryRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(FetchFeedCommand $command): void
    {
        $feed = $this->feedRepository->get($command->feedId);

        if (null === $feed) {
            return;
        }

        try {
            $articles = $this->fetchArticlesFromFeed->__invoke($feed);
        } catch (\Throwable $throwable) {
            $this->logger->notice(
                \sprintf('Unable to fetch articles from feed "%s". Reason: %s', $feed->getName(), $throwable->getMessage()),
                [
                    'feedId' => $feed->getId(),
                    'feedUrl' => $feed->getUrl(),
                ]
            );

            return;
        }

        foreach ($articles as $article) {
            if (null !== $this->entryRepository->ofLink($article->getLink())) {
                continue;
            }

            $this->entryRepository->save($article);
        }
    }
}
