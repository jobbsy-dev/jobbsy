<?php

namespace App\NewsAggregator\Source;

use App\Entity\Article;
use App\NewsAggregator\NewsProviderInterface;
use App\NewsAggregator\RSS\Client;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

abstract class RSSProvider implements NewsProviderInterface
{
    public function __construct(private readonly Client $rssClient)
    {
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     *
     * @return Article[]
     */
    public function retrieve(): array
    {
        $url = $this->getRSSUrl();

        $document = $this->rssClient->get($url);

        if (null === $document) {
            return [];
        }

        $articles = [];
        foreach ($document->getChannels() as $channel) {
            foreach ($channel->getItems() as $item) {
                $article = new Article();
                $article->setTitle(trim($item->title));
                $article->setSource($url);
                $article->setLink($item->link);
                $article->setDescription(trim($item->description));

                if ($item->author) {
                    $article->setAuthors([$item->author]);
                }

                if ($item->pubDate) {
                    $article->setPublishedAt($item->pubDate);
                }

                $articles[] = $article;
            }
        }

        return $articles;
    }

    abstract protected function getRSSUrl(): string;
}
