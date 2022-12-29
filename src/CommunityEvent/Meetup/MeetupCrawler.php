<?php

namespace App\CommunityEvent\Meetup;

use Goutte\Client;

final readonly class MeetupCrawler
{
    public function __construct(private Client $goutteClient)
    {
    }

    /**
     * @throws \JsonException
     *
     * @return array<array{
     *     name: string,
     *     url: string,
     *     description: string,
     *     startDate: string,
     *     endDate: string,
     *     location: array{address: array{addressLocality: string, addressCountry: string}}
     * }>
     */
    public function crawl(string $url): array
    {
        $data = [];
        $crawler = $this->goutteClient->request('GET', $url);

        foreach ($crawler->filter('script[type="application/ld+json"]') as $domElement) {
            $schemas = json_decode($domElement->textContent, true, 512, \JSON_THROW_ON_ERROR);

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
