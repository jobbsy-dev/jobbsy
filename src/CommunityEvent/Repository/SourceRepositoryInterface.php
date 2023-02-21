<?php

namespace App\CommunityEvent\Repository;

use App\Entity\CommunityEvent\Source;

interface SourceRepositoryInterface
{
    /**
     * @return Source[]
     */
    public function getAll(): array;
}
