<?php

namespace App\Provider;

use App\Entity\Job;

final class JobCollection implements \Countable
{
    /**
     * @var Job[]
     */
    private array $jobs = [];

    public function __construct(Job ...$jobs)
    {
        foreach ($jobs as $job) {
            $this->jobs[] = $job;
        }
    }

    public function addJob(Job ...$jobs): void
    {
        foreach ($jobs as $job) {
            $this->jobs[] = $job;
        }
    }

    public function all(): array
    {
        return $this->jobs;
    }

    public function count(): int
    {
        return count($this->jobs);
    }
}
