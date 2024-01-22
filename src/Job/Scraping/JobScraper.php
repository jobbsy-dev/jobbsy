<?php

namespace App\Job\Scraping;

use Symfony\Component\BrowserKit\HttpBrowser;

final readonly class JobScraper
{
    private const string JOB_SCHEMA_TYPE = 'JobPosting';

    public function __construct(private HttpBrowser $httpBrowser)
    {
    }

    public function scrap(string $url): array
    {
        $crawler = $this->httpBrowser->request('GET', $url);

        $structuredData = null;
        foreach ($crawler->filter('script[type="application/ld+json"]') as $domElement) {
            /** @var array $decodedData */
            $decodedData = json_decode($domElement->textContent, true, 512, \JSON_THROW_ON_ERROR);

            if (isset($decodedData['@type']) && self::JOB_SCHEMA_TYPE === $decodedData['@type']) {
                $structuredData = $decodedData;

                break;
            }
        }

        return $structuredData;
    }
}
