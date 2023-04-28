<?php

namespace App\Tests\Repository;

use App\CommunityEvent\SourceRepositoryInterface;
use App\Entity\CommunityEvent\Source;

final class InMemorySourceRepository implements SourceRepositoryInterface
{
    /**
     * @var array<string, Source>
     */
    private array $sources;

    /**
     * @param Source[] $sources
     */
    public function __construct(array $sources)
    {
        foreach ($sources as $source) {
            $this->sources[(string) $source->getId()] = $source;
        }
    }

    public function getAll(): array
    {
        return $this->sources;
    }

    public function get(string $id): ?Source
    {
        return $this->sources[$id] ?? null;
    }
}
