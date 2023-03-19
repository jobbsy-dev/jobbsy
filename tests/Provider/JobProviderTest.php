<?php

namespace App\Tests\Provider;

use App\Entity\Job;
use App\Job\EmploymentType;
use App\Provider\JobProvider;
use App\Provider\SearchParameters;
use PHPUnit\Framework\TestCase;

final class JobProviderTest extends TestCase
{
    public function testRetrieve(): void
    {
        $job1 = new Job(
            'Job 1',
            'Remote',
            EmploymentType::FULL_TIME,
            'Acme',
            'https://example.com'
        );

        $job2 = new Job(
            'Job 1',
            'Remote',
            EmploymentType::FULL_TIME,
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
