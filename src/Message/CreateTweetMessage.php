<?php

namespace App\Message;

final class CreateTweetMessage
{
    public function __construct(public readonly string $jobId, public readonly string $jobUrl)
    {
    }
}
