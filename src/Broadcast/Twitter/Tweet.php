<?php

namespace App\Broadcast\Twitter;

final readonly class Tweet
{
    public function __construct(public string $text)
    {
    }

    /**
     * @return array{text: string}
     */
    public function toArray(): array
    {
        return [
            'text' => $this->text,
        ];
    }
}
