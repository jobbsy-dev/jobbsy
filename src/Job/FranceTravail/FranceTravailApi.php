<?php

namespace App\Job\FranceTravail;

use App\Job\AccessToken;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Service\ResetInterface;

final class FranceTravailApi implements ResetInterface
{
    private ?AccessToken $accessToken = null;

    public function __construct(
        #[Autowire(env: 'POLE_EMPLOI_CLIENT_ID')]
        private readonly string $poleEmploiClientId,
        #[Autowire(env: 'POLE_EMPLOI_CLIENT_SECRET')]
        private readonly string $poleEmploiClientSecret,
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @param string[] $scope
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function authenticate(array $scope = [], bool $force = false): void
    {
        if (false === $force && (null !== $this->accessToken && false === $this->accessToken->hasExpired())) {
            return;
        }

        $response = $this->httpClient->request('POST', 'https://entreprise.francetravail.fr/connexion/oauth2/access_token', [
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

        $data = $response->toArray();

        $this->accessToken = AccessToken::create($data['access_token'], $data['expires_in']);
    }

    /**
     * @param array<string, mixed> $queryParams
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     *
     * @return array<string, scalar|array>
     */
    public function search(array $queryParams = []): array
    {
        $url = 'https://api.francetravail.io/partenaire/offresdemploi/v2/offres/search';

        // Workaround here because this API does not accept DateTime format encoded
        if (\array_key_exists('minCreationDate', $queryParams) || \array_key_exists('maxCreationDate', $queryParams)) {
            /** @var \DateTimeImmutable|null $maxCreationDate */
            $maxCreationDate = $queryParams['maxCreationDate'];
            /** @var \DateTimeImmutable|null $minCreationDate */
            $minCreationDate = $queryParams['minCreationDate'];

            $maxCreationDateQueryUrl = null;
            if (null !== $maxCreationDate) {
                $maxCreationDateQueryUrl = $maxCreationDate->format('Y-m-d\TH:i:s\Z');
            }

            $minCreationDateQueryUrl = null;
            if (null !== $minCreationDate) {
                $minCreationDateQueryUrl = $minCreationDate->format('Y-m-d\TH:i:s\Z');
            }

            $url .= \sprintf(
                '?minCreationDate=%s&maxCreationDate=%s',
                $minCreationDateQueryUrl,
                $maxCreationDateQueryUrl
            );
            unset($queryParams['minCreationDate'], $queryParams['maxCreationDate']);
        }

        $response = $this->httpClient->request('GET', $url, [
            'auth_bearer' => $this->accessToken?->getToken(),
            'query' => $queryParams,
        ]);

        if (200 !== $response->getStatusCode()) {
            return [];
        }

        $data = $response->toArray(false);

        return $data['resultats'] ?? [];
    }

    public function reset(): void
    {
        $this->accessToken = null;
    }
}
