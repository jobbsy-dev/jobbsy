<?php

namespace App\News\Aggregator\RSS\Model;

final class Channel
{
    /**
     * @var Item[]
     */
    private array $items = [];

    public function __construct(
        public readonly string $title,
        public readonly string $link,
        public readonly string $description,
        public readonly ?string $language = null,
        public readonly ?\DateTimeImmutable $pubDate = null,
        ) {
    }

    public function addItem(Item $item): void
    {
        $this->items[] = $item;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public static function create(\DOMXPath $xpath, \DOMNode $channelNode): self
    {
        $channel = new self(
            $xpath->evaluate('./title', $channelNode)->item(0)->nodeValue,
            $xpath->evaluate('./link', $channelNode)->item(0)->nodeValue,
            $xpath->evaluate('./description', $channelNode)->item(0)->nodeValue,
        );

        $itemsNode = $xpath->query('./item', $channelNode);
        foreach ($itemsNode as $itemNode) {
            $channel->addItem(Item::create($xpath, $itemNode));
        }

        return $channel;
    }
}
