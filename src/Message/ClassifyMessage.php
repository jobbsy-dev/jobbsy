<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(transport: 'async')]
final readonly class ClassifyMessage
{
    public function __construct(public string $jobId)
    {
    }
}
