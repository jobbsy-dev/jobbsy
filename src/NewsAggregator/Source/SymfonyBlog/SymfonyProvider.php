<?php

namespace App\NewsAggregator\Source\SymfonyBlog;

use App\Entity\Article;

class SymfonyProvider
{
    public function __construct(private readonly SymfonyClient $client)
    {
    }

    /**
     * @return Article[]
     */
    public function retrieve(): array
    {
        $xmlFeed = $this->client->readFeed();

        if (null === $xmlFeed) {
            return [];
        }

        $document = new \DOMDocument();
        $document->loadXML($xmlFeed);
        $xpath = new \DOMXPath($document);

        $items = $xpath->query('//item');

        $articles = [];
        /** @var \DOMNode $item */
        foreach ($items as $item) {
            $article = new Article();
            $article->setTitle($xpath->query('./title', $item)->item(0)->nodeValue);
            $article->setSource('https://feeds.feedburner.com/symfony/blog');
            //$article->setAuthors([$xpath->query('./creator', $item)->item(0)->nodeValue]);
            $article->setGuid($xpath->query('./guid', $item)->item(0)->nodeValue);
            $article->setLink($xpath->query('./link', $item)->item(0)->nodeValue);
            $article->setDescription($xpath->query('./description', $item)->item(0)->nodeValue);

            $articles[] = $article;
        }

        return $articles;
    }
}
