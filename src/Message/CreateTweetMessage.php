<?php

namespace App\Message;

final class CreateTweetMessage
{
    public function __construct(public readonly string $text)
    {
    }
}
