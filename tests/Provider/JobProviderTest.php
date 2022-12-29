<?php

namespace App\Tests\Provider;

use App\Entity\Job;
use App\Provider\JobProvider;
use App\Provider\SearchParameters;
use PHPUnit\Framework\TestCase;

final class JobProviderTest extends TestCase
{
    public function testRetrieve(): void
    {
        $job1 = new Job();
        $job1->setOrganization('Acme');
        $job1->setLocation('Remote');
        $job1->setUrl('https://example.com');

        $job2 = new Job();
        $job2->setOrganization('SensioLabs');
        $job2->setLocation('Remote');
        $job2->setUrl('https://symfony.com');

        $jobProviders = [
            new InMemoryJobProvider(...[$job1, $job2]),
        ];
        $jobProvider = new JobProvider($jobProviders);

        $jobCollection = $jobProvider->retrieve(new SearchParameters());

        self::assertSame($job1, $jobCollection->all()[0]);
        self::assertSame($job2, $jobCollection->all()[1]);
    }
}
