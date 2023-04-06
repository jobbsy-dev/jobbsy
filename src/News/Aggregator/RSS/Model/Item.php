<?php

namespace App\News\Aggregator\RSS\Model;

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
        if (0 !== $xpath->query('./pubDate', $itemNode)->count()) {
            $pubDate = \DateTimeImmutable::createFromFormat(
                \DateTimeInterface::RFC2822,
                trim((string) $xpath->evaluate('./pubDate', $itemNode)->item(0)->nodeValue)
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
