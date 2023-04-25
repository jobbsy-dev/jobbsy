<?php

namespace App\Sentry;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Client
{
    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire('%env(SENTRY_DSN)%')]
        private string $dsn,
        #[Autowire('%env(SENTRY_ORG)%')]
        private string $organizationSlug,
    ) {
    }

    public function checkIns(CheckInRequest $request): void
    {
        $url = sprintf(
            'https://sentry.io/api/0/organizations/%s/monitors/%s/checkins/',
            $this->organizationSlug,
            $request->monitorSlug
        );

        $this->httpClient->request('POST', $url, [
            'headers' => [
                sprintf('Authorization: DSN %s', $this->dsn),
            ],
            'json' => $request->payload(),
        ]);
    }
}
