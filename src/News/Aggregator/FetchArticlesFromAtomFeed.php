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
        $atomFeed = $this->atomClient->get($feed->getUrl());

        if (null === $atomFeed) {
            return [];
        }

        $articles = [];
        foreach ($atomFeed->getEntries() as $entry) {
            $article = new Entry();

            $title = mb_trim($entry->title);
            if (mb_strlen($title) > 255) {
                continue;
            }

            $article->setTitle(mb_trim($entry->title));
            $article->setLink($entry->link);
            $article->setDescription($entry->summary ? mb_trim((string) $entry->summary) : mb_trim((string) $entry->content));
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
