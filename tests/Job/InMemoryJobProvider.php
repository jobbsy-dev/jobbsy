<?php

namespace App\Tests\Job;

use App\Entity\Job;
use App\Job\JobCollection;
use App\Job\JobProviderInterface;
use App\Job\SearchParameters;

final class InMemoryJobProvider implements JobProviderInterface
{
    /**
     * @var Job[]
     */
    private array $jobs = [];

    public function __construct(Job ...$jobs)
    {
        foreach ($jobs as $job) {
            $this->jobs[(string) $job->getId()] = $job;
        }
    }

    public function retrieve(SearchParameters $parameters): JobCollection
    {
        return new JobCollection(...$this->jobs);
    }

    public function enabled(): bool
    {
        return true;
    }
}
