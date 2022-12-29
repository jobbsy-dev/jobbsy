<?php

namespace App\OpenAI\Model;

final readonly class CompletionRequest
{
    public function __construct(public string $model, public string|array $prompt, public float $temperature = 1)
    {
    }

    /**
     * @return array{model: string, prompt: string|mixed[], temperature: float}
     */
    public function toArray(): array
    {
        return [
            'model' => $this->model,
            'prompt' => $this->prompt,
            'temperature' => $this->temperature,
        ];
    }
}
