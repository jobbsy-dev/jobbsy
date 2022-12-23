<?php

namespace App\OpenAI;

use App\OpenAI\Model\CompletionRequest;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class Client
{
    public function __construct(private HttpClientInterface $openaiClient)
    {
    }

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
