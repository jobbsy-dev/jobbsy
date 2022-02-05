<?php

namespace App\DataFixtures;

use App\EmploymentType;
use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as [$title, $employmentType, $organization, $location, $url, $tags]) {
            $job = new Job();
            $job->setTitle($title);
            $job->setEmploymentType($employmentType);
            $job->setOrganization($organization);
            $job->setLocation($location);
            $job->setUrl($url);
            $job->setTags($tags);
            sleep(1); // just for creation date

            $manager->persist($job);
        }

        $manager->flush();
    }

    private function getData(): \Generator
    {
        yield ['Symfony developer Remote', EmploymentType::FULL_TIME, 'Acme', 'Remote', 'https://example.com', ['symfony6', 'php8']];
        yield ['Lead dev Symfony Paris', EmploymentType::FULL_TIME, 'Acme', 'Paris', 'https://example.com', ['symfony', 'twig']];
    }
}
