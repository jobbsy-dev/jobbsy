<?php

namespace App\News\Aggregator\Atom\Model;

use App\News\Aggregator\XmlHelper;
use Webmozart\Assert\Assert;

final class Feed
{
    /**
     * @var Entry[]
     */
    private array $entries;

    public function __construct(public readonly string $title)
    {
    }

    public function addEntry(Entry $entry): void
    {
        $this->entries[] = $entry;
    }

    /**
     * @return Entry[]
     */
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

        $title = XmlHelper::getNodeValue($xpath, '/atom:feed/atom:title');
        Assert::notNull($title);
        Assert::string($title);

        $feed = new self($title);

        $entries = $xpath->query('/atom:feed/atom:entry');
        Assert::isIterable($entries);

        foreach ($entries as $entryNode) {
            $feed->addEntry(Entry::create($xpath, $entryNode));
        }

        return $feed;
    }
}
