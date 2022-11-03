<?php

namespace App\News\RSS;

use App\News\RSS\Model\Document;
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
