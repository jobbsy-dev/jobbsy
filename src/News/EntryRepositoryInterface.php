<?php

namespace App\News;

use App\Entity\News\Entry;

interface EntryRepositoryInterface
{
    public function save(Entry $entry): void;

    public function remove(Entry $entry): void;

    public function ofLink(string $link): ?Entry;

    /**
     * @return Entry[]
     */
    public function getAll(): array;
}
