<?php

namespace App\NewsAggregator\Source;

use App\Entity\Article;
use App\NewsAggregator\Atom\Client;
use App\NewsAggregator\NewsProviderInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

abstract class AtomProvider implements NewsProviderInterface
{
    public function __construct(private readonly Client $atomClient)
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
        $url = $this->getFeedUrl();

        $feed = $this->atomClient->get($url);

        if (null === $feed) {
            return [];
        }

        $articles = [];
        foreach ($feed->getEntries() as $entry) {
            $article = new Article();
            $article->setTitle(trim($entry->title));
            $article->setSource($url);
            $article->setLink($entry->link);
            $article->setDescription(trim($entry->summary));
            $article->setPublishedAt($entry->published);

            $articles[] = $article;
        }

        return $articles;
    }

    abstract protected function getFeedUrl(): string;
}
