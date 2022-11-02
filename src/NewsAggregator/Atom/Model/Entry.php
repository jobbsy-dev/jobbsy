<?php

namespace App\NewsAggregator\Atom\Model;

final class Entry
{
    public function __construct(
        public readonly string $title,
        public readonly string $link,
        public readonly string $summary,
        public readonly ?\DateTimeImmutable $published = null,
    ) {
    }

    public static function create(\DOMXPath $xpath, \DOMNode $node): self
    {
        $pubDate = null;
        if (0 !== $xpath->query('./atom:published', $node)->count()) {
            $pubDate = \DateTimeImmutable::createFromFormat(
                \DateTimeInterface::ATOM,
                trim($xpath->evaluate('./atom:published', $node)->item(0)->nodeValue)
            );
        }

        return new self(
            $xpath->evaluate('./atom:title', $node)->item(0)->nodeValue,
            self::getLink($xpath, $node),
            $xpath->evaluate('./atom:summary', $node)->item(0)->nodeValue,
            $pubDate
        );
    }

    private static function getLink(\DOMXPath $xpath, \DOMNode $node): string
    {
        return $xpath->query('./atom:link', $node)->item(0)->attributes->getNamedItem('href')->nodeValue;
    }
}
