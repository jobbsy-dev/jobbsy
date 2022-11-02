<?php

namespace App\NewsAggregator\Atom\Model;

final class Author
{
    public function __construct(
        public readonly string $name,
        public readonly string $email
    ) {
    }
}
