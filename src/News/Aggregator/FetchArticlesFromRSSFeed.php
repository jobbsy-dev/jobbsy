<?php

namespace App\News\Aggregator;

use App\Entity\News\Entry;
use App\Entity\News\Feed;
use App\News\Aggregator\RSS\Client as RSSClient;

final readonly class FetchArticlesFromRSSFeed implements FetchArticlesFromFeedInterface
{
    public function __construct(private RSSClient $rssClient)
    {
    }

    public function __invoke(Feed $feed): array
    {
        if (null === $feed->getUrl()) {
            return [];
        }

        $document = $this->rssClient->get($feed->getUrl());

        if (null === $document) {
            return [];
        }

        $articles = [];
        foreach ($document->getChannels() as $channel) {
            foreach ($channel->getItems() as $item) {
                $article = new Entry();
                $article->setTitle(trim((string) $item->title));
                $article->setLink($item->link);
                $article->setDescription(trim((string) $item->description));
                $article->setFeed($feed);

                if ($item->pubDate) {
                    $article->setPublishedAt($item->pubDate);
                }

                $articles[] = $article;
            }
        }

        return $articles;
    }

    public function supports(Feed $feed): bool
    {
        return FeedType::RSS === $feed->getType();
    }
}
