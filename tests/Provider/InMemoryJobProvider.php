<?php

namespace App\Tests\Provider;

use App\Entity\Job;
use App\Provider\JobCollection;
use App\Provider\JobProviderInterface;
use App\Provider\SearchParameters;

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
}
