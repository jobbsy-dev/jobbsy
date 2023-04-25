<?php

namespace App\News\Aggregator\Atom\Model;

use App\News\Aggregator\XmlHelper;
use Webmozart\Assert\Assert;

final readonly class Entry
{
    public function __construct(
        public string $title,
        public string $link,
        public ?string $summary = null,
        public ?string $content = null,
        public ?\DateTimeImmutable $published = null,
    ) {
    }

    public static function create(\DOMXPath $xpath, \DOMNode $node): self
    {
        $pubDate = null;
        $publishedDate = XmlHelper::getNodeValue($xpath, './atom:published', $node);
        if (\is_string($publishedDate)) {
            $pubDate = \DateTimeImmutable::createFromFormat(
                \DateTimeInterface::ATOM,
                trim($publishedDate)
            );
            $pubDate = false === $pubDate ? null : $pubDate;
        }

        Assert::string($link = self::getLink($xpath, $node));
        Assert::string($title = XmlHelper::getNodeValue($xpath, './atom:title', $node));

        $summary = XmlHelper::getNodeValue($xpath, './atom:summary', $node);
        $summary = \is_string($summary) ? $summary : null;

        $content = XmlHelper::getNodeValue($xpath, './atom:content', $node);
        $content = \is_string($content) ? $content : null;

        return new self(
            $title,
            $link,
            $summary,
            $content,
            $pubDate,
        );
    }

    private static function getLink(\DOMXPath $xpath, \DOMNode $node): ?string
    {
        $linkNodes = $xpath->query('./atom:link', $node);

        if (false === $linkNodes) {
            return null;
        }

        if ($linkNodes->length <= 0) {
            return null;
        }

        return $linkNodes
            ->item(0)
            ?->attributes
            ?->getNamedItem('href')
            ?->nodeValue;
    }
}
