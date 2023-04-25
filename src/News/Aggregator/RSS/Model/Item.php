<?php

namespace App\News\Aggregator\RSS\Model;

use App\News\Aggregator\XmlHelper;
use Webmozart\Assert\Assert;

final readonly class Item
{
    public function __construct(
        public string $title,
        public string $link,
        public string $description,
        public ?\DateTimeImmutable $pubDate = null,
        public ?string $author = null,
        public ?string $guid = null,
        public ?string $category = null,
    ) {
    }

    public static function create(\DOMXPath $xpath, \DOMNode $itemNode): self
    {
        $pubDate = null;
        $publishDate = XmlHelper::getNodeValue($xpath, './pubDate', $itemNode);
        Assert::nullOrString($publishDate);

        if (null !== $publishDate) {
            $pubDate = \DateTimeImmutable::createFromFormat(
                \DateTimeInterface::RFC2822,
                trim($publishDate)
            );
            $pubDate = false !== $pubDate ? $pubDate : null;
        }

        Assert::string($title = XmlHelper::getNodeValue($xpath, './title', $itemNode));
        Assert::string($link = XmlHelper::getNodeValue($xpath, './link', $itemNode));
        Assert::string($description = XmlHelper::getNodeValue($xpath, './description', $itemNode));

        return new self(
            $title,
            $link,
            $description,
            $pubDate,
            self::extractAuthor($xpath, $itemNode)
        );
    }

    private static function extractAuthor(\DOMXPath $xpath, \DOMNode $itemNode): ?string
    {
        $author = XmlHelper::getNodeValue($xpath, './author', $itemNode);
        Assert::nullOrString($author);

        if (null !== $author) {
            return $author;
        }

        $xpath->registerNamespace('dc', 'dc');
        $creator = XmlHelper::getNodeValue($xpath, './dc:creator', $itemNode);
        Assert::nullOrString($creator);

        return $creator;
    }
}
