<?php

namespace App\NewsAggregator\Source\SymfonyBlog;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SymfonyClient
{
    public function __construct(private ?HttpClientInterface $httpClient = null)
    {
        if (null === $this->httpClient) {
            $this->httpClient = HttpClient::create();
        }
    }

    public function readFeed(): ?string
    {
        $response = $this->httpClient->request('GET', 'https://feeds.feedburner.com/symfony/blog', [
            'headers' => [
                'Content-Type' => 'text/xml',
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            return null;
        }

        return $response->getContent();
    }
}
