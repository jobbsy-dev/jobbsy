<?php

namespace App\CommunityEvent;

use Symfony\Component\BrowserKit\HttpBrowser;

final readonly class EventScraping
{
    public function __construct(private HttpBrowser $httpBrowser)
    {
    }

    public function fetch(string $url): array
    {
        $data = [];
        $crawler = $this->httpBrowser->request('GET', $url);

        $structuredDataElements = $crawler->filter('script[type="application/ld+json"]');
        /** @var \DOMElement $domElement */
        foreach ($structuredDataElements as $domElement) {
            $schemas = (array) json_decode($domElement->textContent, true, 512, \JSON_THROW_ON_ERROR);

            if (isset($schemas['@type']) && 'Event' === $schemas['@type']) {
                $data[] = $schemas;

                continue;
            }

            /** @var array<string, array<string, string>|string> $schema */
            foreach ($schemas as $schema) {
                if (false === isset($schema['@type'])) {
                    continue;
                }

                if ('Event' !== $schema['@type']) {
                    continue;
                }

                if (false === isset($schema['organizer']['url'])) {
                    continue;
                }

                if (false === str_contains($schema['organizer']['url'], $url)) {
                    continue;
                }

                $data[] = $schema;
            }
        }

        return $data;
    }
}
