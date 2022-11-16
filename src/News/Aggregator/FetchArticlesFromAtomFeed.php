<?php

namespace App\News\Aggregator;

use App\Entity\News\Entry;
use App\Entity\News\Feed;
use App\News\Aggregator\Atom\Client as AtomClient;

final class FetchArticlesFromAtomFeed implements FetchArticlesFromFeedInterface
{
    public function __construct(private readonly AtomClient $atomClient)
    {
    }

    public function __invoke(Feed $feed): array
    {
        $atomFeed = $this->atomClient->get($feed->getUrl());

        if (null === $atomFeed) {
            return [];
        }

        $articles = [];
        foreach ($atomFeed->getEntries() as $entry) {
            $article = new Entry();
            $article->setTitle(trim($entry->title));
            $article->setLink($entry->link);
            $article->setDescription($entry->summary ? trim($entry->summary) : trim($entry->content));
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
