<?php

namespace App\Provider\RemoteOK;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class RemoteOKApi
{
    public function __construct(private ?HttpClientInterface $httpClient = null)
    {
        if (null === $this->httpClient) {
            $this->httpClient = HttpClient::create();
        }
    }

    public function search(array $queryParams): array
    {
        $response = $this->httpClient->request('GET', 'https://remoteok.com/api', [
            'query' => $queryParams,
        ]);

        if (200 !== $response->getStatusCode()) {
            return [];
        }

        return $response->toArray(false);
    }
}
