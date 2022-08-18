<?php

namespace App\Tests\Mock;

use Stripe\HttpClient\ClientInterface;

final class MockStripeClient implements ClientInterface
{
    public function __construct(
        private readonly ?string $createSessionResponse = null,
        private readonly ?string $retrieveSessionResponse = null
    ) {
    }

    public function request($method, $absUrl, $headers, $params, $hasFile): array
    {
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
