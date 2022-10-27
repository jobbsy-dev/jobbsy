<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\Job;
use App\Job\EmploymentType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class AppFixtures extends Fixture
{
    public const JOB_1_ID = 'fe094a22-5b0f-4f4d-88ee-5b331aeb6675';
    public const JOB_2_ID = '6bb57d15-313d-403f-8785-58ebebc61852';

    public const EVENT_1_ID = '9594a801-ff90-4be5-a3b4-ff8497f13ecf';

    public function load(ObjectManager $manager): void
    {
        $this->loadJobs($manager);
        $this->loadEvents($manager);
    }

    private function loadJobs(ObjectManager $manager): void
    {
        $publishedAt = new \DateTimeImmutable();
        foreach ($this->getJobData() as [$title, $employmentType, $organization, $location, $url, $tags, $id, $pinned]) {
            $job = new Job($id ? Uuid::fromString($id) : null);
            $job->setTitle($title);
            $job->setEmploymentType($employmentType);
            $job->setOrganization($organization);
            $job->setLocation($location);
            $job->setUrl($url);
            $job->setTags($tags);
            $job->publish($publishedAt);
            if ($pinned) {
                $job->pinUntil(new \DateTimeImmutable('+1 month'));
            }
            $publishedAt = $publishedAt->modify('- 1 hour');

            $manager->persist($job);
        }

        $manager->flush();
    }

    private function loadEvents(ObjectManager $manager): void
    {
        foreach ($this->getEventData() as [$id, $name, $startDate, $endDate, $location, $abstract, $url, $countryCode]) {
            $event = new Event($id ? Uuid::fromString($id) : null);
            $event->setName($name);
            $event->setUrl($url);
            $event->setAbstract($abstract);
            $event->setLocation($location);
            $event->setStartDate(\DateTimeImmutable::createFromFormat('Y-m-d', $startDate));
            $event->setEndDate(\DateTimeImmutable::createFromFormat('Y-m-d', $endDate));
            $event->setCountry($countryCode);

            $manager->persist($event);
        }

        $manager->flush();
    }

    private function getJobData(): \Generator
    {
        yield [
            'Symfony developer Remote',
            EmploymentType::FULL_TIME,
            'Acme',
            'Remote',
            'https://example.com',
            ['symfony6', 'php8'],
            self::JOB_1_ID,
            true,
        ];
        yield [
            'Lead dev Symfony Paris',
            EmploymentType::FULL_TIME,
            'Acme',
            'Paris',
            'https://example.com',
            ['symfony', 'twig'],
            self::JOB_2_ID,
            false,
        ];
        yield [
            'Backend Symfony developer',
            EmploymentType::FULL_TIME,
            'SensioLabs',
            'Paris',
            'https://example.com',
            ['symfony', 'twig'],
            null,
            false,
        ];
    }

    private function getEventData(): \Generator
    {
        yield [
            self::EVENT_1_ID,
            'SymfonyCon 2022 Disneyland Paris',
            '2022-11-17',
            '2022-11-18',
            'Paris',
            'We are thrilled to welcome you at SymfonyCon Disneyland Paris 2022! This year, we will finally meet you at the Disney\'s Hotel New York - Art of Marvel for the annual international Symfony conference. ',
            'https://live.symfony.com/2022-paris-con/',
            'FR',
        ];

        yield [
            null,
            'API Platform Conference 2022',
            '2022-09-15',
            '2022-09-16',
            'Lille & Online',
            'The 2nd edition of the API Platform Conference (a popular open source framework for building hypermedia and GraphQL APIs ) is coming!',
            'https://api-platform.com/con/2022/',
            'FR',
        ];

        yield [
            null,
            'SymfonyWorld Online 2022 Winter Edition',
            '2022-12-08',
            '2022-12-09',
            'Online',
            'Join us for the fifth edition of the international online SymfonyWorld conference. The entire conference will take place online during 4 days in English.',
            'https://live.symfony.com/2022-world-winter/',
            'FR',
        ];
    }
}
