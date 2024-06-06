<?php

namespace App\Tests\Repository;

use App\Entity\Job;
use App\Job\Repository\JobRepositoryInterface;
use App\Repository\JobNotFoundException;
use Ramsey\Uuid\UuidInterface;

final class InMemoryJobRepository implements JobRepositoryInterface
{
    /**
     * @var array<string, Job>
     */
    private array $jobs;

    /**
     * @param Job[] $jobs
     */
    public function __construct(array $jobs)
    {
        foreach ($jobs as $job) {
            $this->jobs[(string) $job->getId()] = $job;
        }
    }

    public function get(UuidInterface $id): Job
    {
        if (isset($this->jobs[(string) $id])) {
            return $this->jobs[(string) $id];
        }

        throw new JobNotFoundException();
    }

    public function save(Job $job): void
    {
        $this->jobs[(string) $job->getId()] = $job;
    }

    public function remove(Job $job): void
    {
        unset($this->jobs[(string) $job->getId()]);
    }
}
