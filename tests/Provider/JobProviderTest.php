<?php

namespace App\Tests\Provider;

use App\Entity\Job;
use App\Provider\JobProvider;
use App\Provider\SearchParameters;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class JobProviderTest extends TestCase
{
    public function testRetrieve(): void
    {
        $job1 = new Job(Uuid::fromString('b2b10e48-e5a3-4175-bceb-86665232ef97'));
        $job1->setOrganization('Acme');
        $job1->setLocation('Remote');
        $job1->setUrl('https://example.com');

        $job2 = new Job(Uuid::fromString('b7ba3264-5c36-422f-adae-d0349d0f6e16'));
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
