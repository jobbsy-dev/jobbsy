<?php

declare(strict_types=1);

namespace App\News;

final readonly class FetchFeedCommand
{
    public function __construct(public string $feedId)
    {
    }
}
