<?php

namespace App\CommunityEvent\Repository;

use App\CommunityEvent\SourceType;
use App\Entity\CommunityEvent\Source;

interface SourceRepositoryInterface
{
    /**
     * @return Source[]
     */
    public function findByType(SourceType $sourceType): array;

    public function save(Source $entity, bool $flush = false): void;

    public function remove(Source $entity, bool $flush = false): void;
}
