<?php

namespace App\News\Aggregator;

use App\Entity\News\Entry;
use App\Entity\News\Feed;
use App\News\Aggregator\Atom\Client as AtomClient;

final readonly class FetchArticlesFromAtomFeed implements FetchArticlesFromFeedInterface
{
    public function __construct(private AtomClient $atomClient)
    {
    }

    public function __invoke(Feed $feed): array
    {
        if (null === $feed->getUrl()) {
            return [];
        }

        $atomFeed = $this->atomClient->get($feed->getUrl());

        if (null === $atomFeed) {
            return [];
        }

        $articles = [];
        foreach ($atomFeed->getEntries() as $entry) {
            $article = new Entry();
            $article->setTitle(trim((string) $entry->title));
            $article->setLink($entry->link);
            $article->setDescription($entry->summary ? trim((string) $entry->summary) : trim((string) $entry->content));
            $article->setPublishedAt($entry->published);
            $article->setFeed($feed);

            $articles[] = $article;
        }

        return $articles;
    }

    public function supports(Feed $feed): bool
    {
        return FeedType::ATOM === $feed->getType();
    }
}
