<?php

namespace App\Provider\Arbeitsagentur;

use App\Provider\AccessToken;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ArbeitsagenturApi
{
    private ?AccessToken $accessToken = null;

    public function __construct(
        private readonly string $arbeitsagenturClientId,
        private readonly string $arbeitsagenturClientSecret,
        private ?HttpClientInterface $httpClient = null,
    ) {
        if (null === $this->httpClient) {
            $this->httpClient = HttpClient::create();
        }
    }

    public function authenticate(): void
    {
        $response = $this->httpClient->request('POST', 'https://rest.arbeitsagentur.de/oauth/gettoken_cc', [
            'body' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->arbeitsagenturClientId,
                'client_secret' => $this->arbeitsagenturClientSecret,
            ],
            'query' => [
                'realm' => '/partenaire',
            ],
        ]);

        $data = $response->toArray();

        $this->accessToken = AccessToken::create($data['access_token'], $data['expires_in']);
    }

    public function search(array $queryParams = []): array
    {
        $response = $this->httpClient->request('GET', 'https://rest.arbeitsagentur.de/jobboerse/jobsuche-service/pc/v4/jobs', [
            'auth_bearer' => $this->accessToken?->getToken(),
            'query' => $queryParams,
        ]);

        if (200 !== $response->getStatusCode()) {
            return [];
        }

        $data = $response->toArray(false);

        return $data['stellenangebote'] ?? [];
    }

    public function getOrganizationLogo(string $hashId): ?OrganizationLogo
    {
        $response = $this->httpClient->request('GET', 'https://rest.arbeitsagentur.de/jobboerse/jobsuche-service/ed/v1/arbeitgeberlogo/'.$hashId, [
            'auth_bearer' => $this->accessToken?->getToken(),
        ]);

        if (200 !== $response->getStatusCode()) {
            return null;
        }

        return new OrganizationLogo(
            $response->getContent(false),
            $response->getHeaders(false)['content-type'][0] ?? null
        );
    }
}
