<?php

namespace App\DataFixtures;

use App\EmploymentType;
use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class AppFixtures extends Fixture
{
    public const JOB_1_ID = 'fe094a22-5b0f-4f4d-88ee-5b331aeb6675';

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as [$title, $employmentType, $organization, $location, $url, $tags, $id, $pinned]) {
            $job = new Job($id ? Uuid::fromString($id) : null);
            $job->setTitle($title);
            $job->setEmploymentType($employmentType);
            $job->setOrganization($organization);
            $job->setLocation($location);
            $job->setUrl($url);
            $job->setTags($tags);
            if ($pinned) {
                $job->pinUntil(new \DateTimeImmutable('+1 month'));
            }
            sleep(1); // just for creation date

            $manager->persist($job);
        }

        $manager->flush();
    }

    private function getData(): \Generator
    {
        yield ['Symfony developer Remote', EmploymentType::FULL_TIME, 'Acme', 'Remote', 'https://example.com', ['symfony6', 'php8'], self::JOB_1_ID, true];
        yield ['Lead dev Symfony Paris', EmploymentType::FULL_TIME, 'Acme', 'Paris', 'https://example.com', ['symfony', 'twig'], null, null];
        yield ['Backend Symfony developer', EmploymentType::FULL_TIME, 'SensioLabs', 'Paris', 'https://example.com', ['symfony', 'twig'], null, null];
    }
}
