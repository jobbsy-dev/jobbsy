<?php

namespace App\Tests\Mock;

use Stripe\HttpClient\ClientInterface;

final readonly class MockStripeClient implements ClientInterface
{
    public function __construct(
        private ?string $createSessionResponse = null,
        private ?string $retrieveSessionResponse = null,
    ) {
    }

    public function request(
        $method,
        $absUrl,
        $headers,
        $params,
        $hasFile,
        $apiMode = 'v1',
        $maxNetworkRetries = null,
    ): array {
        $body = '{}';

        if ('https://api.stripe.com/v1/checkout/sessions' === $absUrl && 'post' === mb_strtolower($method)) {
            $body = $this->createSessionResponse;
        }

        if (str_starts_with($absUrl, 'https://api.stripe.com/v1/checkout/sessions') && 'get' === mb_strtolower($method)) {
            $body = $this->retrieveSessionResponse;
        }

        return [$body, 200, []];
    }
}
