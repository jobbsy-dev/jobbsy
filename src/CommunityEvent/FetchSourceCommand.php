<?php

declare(strict_types=1);

namespace App\CommunityEvent;

use App\Shared\AsyncMessageInterface;

final readonly class FetchSourceCommand implements AsyncMessageInterface
{
    public function __construct(public string $sourceId)
    {
    }
}
