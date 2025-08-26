<?php

declare(strict_types=1);

namespace App\CommunityEvent;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(transport: 'async')]
final readonly class FetchSourceCommand
{
    public function __construct(public string $sourceId)
    {
    }
}
