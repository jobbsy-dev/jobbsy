<?php

namespace App\News\Aggregator\Atom\Model;

final readonly class Author
{
    public function __construct(
        public string $name,
        public string $email
    ) {
    }
}
