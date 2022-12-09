<?php

namespace App\News\Aggregator\RSS;

use App\News\Aggregator\RSS\Model\Document;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Client
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function get(string $url): ?Document
    {
        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'Content-Type' => 'text/xml',
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            return null;
        }

        $xmlData = $response->getContent();

        return Document::create($xmlData);
    }
}
