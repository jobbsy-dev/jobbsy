<?php

namespace App\CommunityEvent;

use App\Entity\CommunityEvent\Source;

interface SourceRepositoryInterface
{
    /**
     * @return Source[]
     */
    public function getAll(): array;

    public function get(string $id): ?Source;
}
