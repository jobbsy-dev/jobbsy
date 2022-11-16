<?php

namespace App\News\Aggregator\Atom;

use App\News\Aggregator\Atom\Model\Feed;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class Client
{
    public function __construct(private ?HttpClientInterface $httpClient = null)
    {
        if (null === $this->httpClient) {
            $this->httpClient = HttpClient::create();
        }
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
