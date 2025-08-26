<?php

declare(strict_types=1);

namespace App\News;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(transport: 'async')]
final readonly class FetchFeedCommand
{
    public function __construct(public string $feedId)
    {
    }
}
