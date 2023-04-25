<?php

namespace App\News\Aggregator\RSS\Model;

use App\News\Aggregator\XmlHelper;
use Webmozart\Assert\Assert;

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

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public static function create(\DOMXPath $xpath, \DOMNode $channelNode): self
    {
        Assert::string($title = XmlHelper::getNodeValue($xpath, './title', $channelNode));
        Assert::string($link = XmlHelper::getNodeValue($xpath, './link', $channelNode));
        Assert::string($description = XmlHelper::getNodeValue($xpath, './description', $channelNode));

        $channel = new self(
            $title,
            $link,
            $description,
        );

        $itemsNode = $xpath->query('./item', $channelNode);

        if (false === $itemsNode) {
            return $channel;
        }

        Assert::isIterable($itemsNode);

        foreach ($itemsNode as $itemNode) {
            $channel->addItem(Item::create($xpath, $itemNode));
        }

        return $channel;
    }
}
