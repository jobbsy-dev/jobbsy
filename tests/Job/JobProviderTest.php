<?php

namespace App\Tests\Job;

use App\Entity\Job;
use App\Job\EmploymentType;
use App\Job\JobProvider;
use App\Job\SearchParameters;
use PHPUnit\Framework\TestCase;

final class JobProviderTest extends TestCase
{
    public function test_retrieve(): void
    {
        $job1 = new Job(
            'Job 1',
            'Remote',
            EmploymentType::FULLTIME,
            'Acme',
            'https://example.com'
        );

        $job2 = new Job(
            'Job 1',
            'Remote',
            EmploymentType::FULLTIME,
            'Symfony',
            'https://symfony.com'
        );

        $jobProviders = [
            new InMemoryJobProvider(...[$job1, $job2]),
        ];
        $jobProvider = new JobProvider($jobProviders);

        $jobCollection = $jobProvider->retrieve(new SearchParameters());

        self::assertSame($job1, $jobCollection->all()[0]);
        self::assertSame($job2, $jobCollection->all()[1]);
    }
}
