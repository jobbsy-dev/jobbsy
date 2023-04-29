<?php

declare(strict_types=1);

namespace App\News;

use App\Shared\AsyncMessageInterface;

final readonly class FetchFeedCommand implements AsyncMessageInterface
{
    public function __construct(public string $feedId)
    {
    }
}
