<?php

namespace App\Provider\RemoteOK;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class RemoteOKApi
{
    public function __construct(private HttpClientInterface $httpClient)
    {
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
