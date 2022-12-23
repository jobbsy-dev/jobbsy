<?php

namespace App\OpenAI\Model;

final readonly class CompletionRequest
{
    public function __construct(
        public string $model,
        public string|array $prompt,
        public float $temperature = 1,
    ) {
    }

    public function toArray(): array
    {
        return [
            'model' => $this->model,
            'prompt' => $this->prompt,
            'temperature' => $this->temperature,
        ];
    }
}
