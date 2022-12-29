<?php

namespace App\Job\Repository;

use App\Entity\Job;
use Ramsey\Uuid\UuidInterface;

interface JobRepositoryInterface
{
    public function get(UuidInterface $id): Job;

    public function save(Job $entity, bool $flush = false): void;

    public function remove(Job $entity, bool $flush = false): void;
}
