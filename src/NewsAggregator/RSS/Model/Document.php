<?php

namespace App\NewsAggregator\RSS\Model;

final class Document
{
    /**
     * @var Channel[]
     */
    private array $channels = [];

    public function __construct(public readonly string $version = '2.0')
    {
    }

    public function addChannel(Channel $channel): void
    {
        $this->channels[] = $channel;
    }

    public function getChannels(): array
    {
        return $this->channels;
    }

    public static function create(string $content): self
    {
        $rssDocument = new self();

        $document = new \DOMDocument();
        $document->loadXML($content);
        $xpath = new \DOMXPath($document);

        $channelsNode = $xpath->query('/rss/channel');

        foreach ($channelsNode as $channelNode) {
            $rssDocument->addChannel(Channel::create($xpath, $channelNode));
        }

        return $rssDocument;
    }
}
