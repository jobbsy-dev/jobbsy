<?php

namespace App\Message;

final readonly class CreateTweetMessage
{
    public function __construct(public string $jobId, public string $jobUrl)
    {
    }
}
