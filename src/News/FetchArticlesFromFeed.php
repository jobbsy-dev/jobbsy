<?php

namespace App\News;

use App\Entity\Article;
use App\Entity\Feed;
use App\News\Atom\Client as AtomClient;
use App\News\RSS\Client as RSSClient;

final class FetchArticlesFromFeed
{
    public function __construct(
        private readonly AtomClient $atomClient,
        private readonly RSSClient $rssClient
    ) {
    }

    public function __invoke(Feed $feed): array
    {
        return match ($feed->getType()) {
            FeedType::RSS => $this->createFromRSS($feed),
            FeedType::ATOM => $this->createFromAtom($feed),
            default => [],
        };
    }

    private function createFromRSS(Feed $feed): array
    {
        $document = $this->rssClient->get($feed->getUrl());

        if (null === $document) {
            return [];
        }

        $articles = [];
        foreach ($document->getChannels() as $channel) {
            foreach ($channel->getItems() as $item) {
                $article = new Article($feed);
                $article->setTitle(trim($item->title));
                $article->setLink($item->link);
                $article->setDescription(trim($item->description));

                if ($item->pubDate) {
                    $article->setPublishedAt($item->pubDate);
                }

                $articles[] = $article;
            }
        }

        return $articles;
    }

    private function createFromAtom(Feed $feed): array
    {
        $atomFeed = $this->atomClient->get($feed->getUrl());

        if (null === $atomFeed) {
            return [];
        }

        $articles = [];
        foreach ($atomFeed->getEntries() as $entry) {
            $article = new Article($feed);
            $article->setTitle(trim($entry->title));
            $article->setLink($entry->link);
            $article->setDescription(trim($entry->summary));
            $article->setPublishedAt($entry->published);

            $articles[] = $article;
        }

        return $articles;
    }
}
