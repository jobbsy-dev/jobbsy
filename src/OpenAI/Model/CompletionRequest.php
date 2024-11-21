<?php

namespace App\OpenAI\Model;

final readonly class CompletionRequest
{
    /**
     * @param string|string[] $prompt
     */
    public function __construct(
        public string $model,
        public string|array $prompt,
        public float $temperature = 1,
        public int $maxTokens = 16,
    ) {
    }

    /**
     * @return array{model: string, prompt: string|mixed[], temperature: float, max_tokens: int}
     */
    public function toArray(): array
    {
        return [
            'model' => $this->model,
            'prompt' => $this->prompt,
            'temperature' => $this->temperature,
            'max_tokens' => $this->maxTokens,
        ];
    }
}
