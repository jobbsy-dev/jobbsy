<?php

namespace App\News\Atom\Model;

use Webmozart\Assert\Assert;

final class Feed
{
    /**
     * @var Entry[]
     */
    private array $entries;

    public function __construct(
        public readonly string $title
    ) {
    }

    public function addEntry(Entry $entry): void
    {
        $this->entries[] = $entry;
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    public static function create(string $content): self
    {
        $document = new \DOMDocument();
        $document->loadXML($content);
        $xpath = new \DOMXPath($document);

        $xpath->registerNamespace('atom', 'http://www.w3.org/2005/Atom');

        Assert::notNull($xpath->evaluate('/atom:feed/atom:title')->item(0)->nodeValue);

        $feed = new self($xpath->evaluate('/atom:feed/atom:title')->item(0)->nodeValue);

        $entries = $xpath->query('/atom:feed/atom:entry');

        foreach ($entries as $entryNode) {
            $feed->addEntry(Entry::create($xpath, $entryNode));
        }

        return $feed;
    }
}
