<?php

namespace App\Tests\Repository;

use App\Entity\News\Entry;
use App\News\EntryRepositoryInterface;

final class InMemoryEntryRepository implements EntryRepositoryInterface
{
    /**
     * @var array<string, Entry>
     */
    private array $entries = [];

    /**
     * @param Entry[] $entries
     */
    public function __construct(array $entries = [])
    {
        foreach ($entries as $entry) {
            $this->entries[(string) $entry->getId()] = $entry;
        }
    }

    public function save(Entry $entry): void
    {
        $this->entries[(string) $entry->getId()] = $entry;
    }

    public function remove(Entry $entry): void
    {
        // TODO: Implement remove() method.
    }

    public function ofLink(string $link): ?Entry
    {
        foreach ($this->entries as $entry) {
            if ($entry->getLink() === $link) {
                return $entry;
            }
        }

        return null;
    }

    public function getAll(): array
    {
        return $this->entries;
    }
}
