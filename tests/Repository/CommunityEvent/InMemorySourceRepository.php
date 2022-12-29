<?php

namespace App\Tests\Repository\CommunityEvent;

use App\CommunityEvent\Repository\SourceRepositoryInterface;
use App\CommunityEvent\SourceType;
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

    public function save(Source $entity, bool $flush = false): void
    {
        $this->sources[] = $entity;
    }

    public function remove(Source $entity, bool $flush = false): void
    {
        unset($this->sources[(string) $entity->getId()]);
    }

    public function findByType(SourceType $sourceType): array
    {
        $sources = [];

        foreach ($this->sources as $source) {
            if ($source->getType() === $sourceType) {
                $sources[] = $source;
            }
        }

        return $sources;
    }
}
