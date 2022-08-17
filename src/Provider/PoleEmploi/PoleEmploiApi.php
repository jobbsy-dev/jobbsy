<?php

namespace App\Provider\PoleEmploi;

use App\Provider\AccessToken;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PoleEmploiApi
{
    private ?AccessToken $accessToken = null;

    public function __construct(
        #[Autowire('%env(POLE_EMPLOI_CLIENT_ID)%')]
        private readonly string $poleEmploiClientId,
        #[Autowire('%env(POLE_EMPLOI_CLIENT_SECRET)%')]
        private readonly string $poleEmploiClientSecret,
        private ?HttpClientInterface $httpClient = null,
    ) {
        if (null === $this->httpClient) {
            $this->httpClient = HttpClient::create();
        }
    }

    public function authenticate(array $scope = [], bool $force = false): void
    {
        if (false === $force && (null !== $this->accessToken && false === $this->accessToken->hasExpired())) {
            return;
        }

        $response = $this->httpClient->request('POST', 'https://entreprise.pole-emploi.fr/connexion/oauth2/access_token', [
            'body' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->poleEmploiClientId,
                'client_secret' => $this->poleEmploiClientSecret,
                'scope' => implode(' ', $scope),
            ],
            'query' => [
                'realm' => '/partenaire',
            ],
        ]);
        if (200 !== $response->getStatusCode()) {
            echo 'Une erreur est survenue '.$response->getStatusCode()."\r\n";
            echo $response->getContent()."\r\n";
        }
        $data = $response->toArray();

        $this->accessToken = AccessToken::create($data['access_token'], $data['expires_in']);
    }

    public function search(array $queryParams = []): array
    {
        $url = 'https://api.emploi-store.fr/partenaire/offresdemploi/v2/offres/search';

        // Workaround here because this API does not accept DateTime format encoded
        if (\array_key_exists('minCreationDate', $queryParams) || \array_key_exists('maxCreationDate', $queryParams)) {
            $maxCreationDate = $queryParams['maxCreationDate'];
            $minCreationDate = $queryParams['minCreationDate'];

            $maxCreationDateQueryUrl = $maxCreationDate;
            if ($maxCreationDate instanceof \DateTimeInterface) {
                $maxCreationDateQueryUrl = $maxCreationDate->format('Y-m-d\TH:i:s\Z');
            }

            $minCreationDateQueryUrl = $minCreationDate;
            if ($minCreationDate instanceof \DateTimeInterface) {
                $minCreationDateQueryUrl = $minCreationDate->format('Y-m-d\TH:i:s\Z');
            }

            $url .= sprintf(
                '?minCreationDate=%s&maxCreationDate=%s',
                $minCreationDateQueryUrl,
                $maxCreationDateQueryUrl
            );
            unset($queryParams['minCreationDate'], $queryParams['maxCreationDate']);
        }
        try {
            $response = $this->httpClient->request('GET', $url, [
                'auth_bearer' => $this->accessToken?->getToken(),
                'query' => $queryParams,
            ]);
            if (200 !== $response->getStatusCode()) {
                throw new TransportException('Une erreur est survenue '.$response->getStatusCode()."\r\n");
            }
        } catch (TransportException $e) {
            echo $e->getMessage()."\r\n";

            return [];
        }

        $data = $response->toArray(false);

        return $data['resultats'] ?? [];
    }
}
