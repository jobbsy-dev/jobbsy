<?php

namespace App\Message\Job;

final readonly class ClassifyMessage
{
    public function __construct(public string $jobId)
    {
    }
}
