<?php

declare(strict_types=1);

namespace App\News;

use App\News\Aggregator\FetchArticlesFromFeed;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class FetchFeedCommandHandler
{
    public function __construct(
        private FeedRepositoryInterface $feedRepository,
        private FetchArticlesFromFeed $fetchArticlesFromFeed,
        private EntryRepositoryInterface $entryRepository,
    ) {
    }

    public function __invoke(FetchFeedCommand $command): void
    {
        $feed = $this->feedRepository->get($command->feedId);

        if (null === $feed) {
            return;
        }

        $articles = $this->fetchArticlesFromFeed->__invoke($feed);

        foreach ($articles as $article) {
            if (null !== $this->entryRepository->ofLink($article->getLink())) {
                continue;
            }

            $this->entryRepository->save($article);
        }
    }
}
