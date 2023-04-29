<?php

namespace App\Message\Job;

use App\Shared\AsyncMessageInterface;

final readonly class ClassifyMessage implements AsyncMessageInterface
{
    public function __construct(public string $jobId)
    {
    }
}
