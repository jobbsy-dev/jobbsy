<?php

namespace App\Analytics\Plausible;

use App\Analytics\AnalyticsClient;
use App\Analytics\EventRequestInterface;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class PlausibleClient implements AnalyticsClient
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function event(EventRequestInterface $request): void
    {
        $response = $this->httpClient->request('POST', 'https://analytics.jobbsy.dev/api/event', [
            'headers' => $request->headers(),
            'json' => $request->body(),
        ]);

        $status = $response->getStatusCode();

        if (202 !== $status) {
            throw new TransportException('Unable to create event.');
        }
    }
}
