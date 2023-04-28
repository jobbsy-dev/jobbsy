<?php

declare(strict_types=1);

namespace App\CommunityEvent;

final readonly class FetchSourceCommand
{
    public function __construct(public string $sourceId)
    {
    }
}
