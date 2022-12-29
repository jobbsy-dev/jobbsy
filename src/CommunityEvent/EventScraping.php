<?php

namespace App\CommunityEvent;

use Goutte\Client;

final readonly class EventScraping
{
    public function __construct(private Client $goutteClient)
    {
    }

    public function fetch(string $url): array
    {
        $data = [];
        $crawler = $this->goutteClient->request('GET', $url);

        $structuredDataElements = $crawler->filter('script[type="application/ld+json"]');
        foreach ($structuredDataElements as $domElement) {
            $schemas = json_decode($domElement->textContent, true, 512, \JSON_THROW_ON_ERROR);

            if (isset($schemas['@type']) && 'Event' === $schemas['@type']) {
                $data[] = $schemas;

                continue;
            }

            foreach ($schemas as $schema) {
                if (false === isset($schema['@type'])) {
                    continue;
                }

                if ('Event' !== $schema['@type']) {
                    continue;
                }

                $data[] = $schema;
            }
        }

        return $data;
    }
}
