<?php

namespace App\Provider\Scraping;

use Goutte\Client;

final readonly class JobScraper
{
    private const JOB_SCHEMA_TYPE = 'JobPosting';

    public function __construct(private Client $goutteClient)
    {
    }

    public function scrap(string $url): array
    {
        $crawler = $this->goutteClient->request('GET', $url);

        $structuredData = null;
        foreach ($crawler->filter('script[type="application/ld+json"]') as $domElement) {
            $decodedData = json_decode($domElement->textContent, true, 512, \JSON_THROW_ON_ERROR);

            if (isset($decodedData['@type']) && self::JOB_SCHEMA_TYPE === $decodedData['@type']) {
                $structuredData = $decodedData;

                break;
            }
        }

        return $structuredData;
    }
}
