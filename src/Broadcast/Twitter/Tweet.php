<?php

namespace App\Broadcast\Twitter;

final class Tweet
{
    public function __construct(public readonly string $text)
    {
    }

    public function toArray(): array
    {
        return [
            'text' => $this->text,
        ];
    }
}
