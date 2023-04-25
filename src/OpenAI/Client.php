<?php

namespace App\OpenAI;

use App\OpenAI\Model\CompletionRequest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Client
{
    public function __construct(private HttpClientInterface $openaiClient)
    {
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     *
     * @return array<string, mixed|array|int>
     */
    public function completions(CompletionRequest $request): array
    {
        $response = $this->openaiClient->request('POST', 'completions', [
            'json' => $request->toArray(),
        ]);

        if (200 !== $response->getStatusCode()) {
            return [];
        }

        return $response->toArray(false);
    }
}
