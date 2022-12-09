<?php

namespace App\Broadcast\Twitter;

final readonly class Tweet
{
    public function __construct(public string $text)
    {
    }

    public function toArray(): array
    {
        return [
            'text' => $this->text,
        ];
    }
}
