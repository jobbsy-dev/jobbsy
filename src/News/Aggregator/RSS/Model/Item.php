<?php

namespace App\News\Aggregator\RSS\Model;

final class Item
{
    public function __construct(
        public readonly string $title,
        public readonly string $link,
        public readonly string $description,
        public readonly ?\DateTimeImmutable $pubDate = null,
        public readonly ?string $author = null,
        public readonly ?string $guid = null,
        public readonly ?string $category = null,
    ) {
    }

    public static function create(\DOMXPath $xpath, \DOMNode $itemNode): self
    {
        $pubDate = null;
        if (0 !== $xpath->query('./pubDate', $itemNode)->count()) {
            $pubDate = \DateTimeImmutable::createFromFormat(
                \DateTimeInterface::RFC2822,
                trim($xpath->evaluate('./pubDate', $itemNode)->item(0)->nodeValue)
            );
        }

        return new self(
            $xpath->evaluate('./title', $itemNode)->item(0)->nodeValue,
            $xpath->evaluate('./link', $itemNode)->item(0)->nodeValue,
            $xpath->evaluate('./description', $itemNode)->item(0)->nodeValue,
            $pubDate,
            self::extractAuthor($xpath, $itemNode)
        );
    }

    private static function extractAuthor(\DOMXPath $xpath, \DOMNode $itemNode): ?string
    {
        if (0 !== $xpath->query('./author', $itemNode)->count()) {
            return $xpath->evaluate('./author', $itemNode)->item(0)->nodeValue;
        }

        $xpath->registerNamespace('dc', 'dc');

        if (0 !== $xpath->query('./dc:creator', $itemNode)->count()) {
            return $xpath->evaluate('./dc:creator', $itemNode)->item(0)->nodeValue;
        }

        return null;
    }
}
