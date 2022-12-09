<?php

namespace App\News\Aggregator\Atom;

use App\News\Aggregator\Atom\Model\Feed;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Client
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function get(string $url): ?Feed
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

        return Feed::create($xmlData);
    }
}
