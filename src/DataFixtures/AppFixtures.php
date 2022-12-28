<?php

namespace App\DataFixtures;

use App\CommunityEvent\AttendanceMode;
use App\Entity\CommunityEvent\Event;
use App\Entity\Job;
use App\Entity\News\Entry;
use App\Entity\News\Feed;
use App\Job\EmploymentType;
use App\News\Aggregator\FeedType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class AppFixtures extends Fixture
{
    public const JOB_1_ID = 'fe094a22-5b0f-4f4d-88ee-5b331aeb6675';
    public const JOB_2_ID = '6bb57d15-313d-403f-8785-58ebebc61852';

    public const EVENT_1_ID = '9594a801-ff90-4be5-a3b4-ff8497f13ecf';

    public const FEED_RSS_ID = '02f25238-795d-42b9-a569-cb79531a6195';
    public const FEED_ATOM_ID = '5492e5a8-2865-486b-b9c0-f8313fdfcb24';
    public const NEWS_1_ID = '867fd3ce-77d2-4447-a97e-643ddec9f435';

    public function load(ObjectManager $manager): void
    {
        $this->loadJobs($manager);
        $this->loadEvents($manager);
        $this->loadNews($manager);
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
            $publishedAt = $publishedAt->modify('- 1 hour');
            $job->publish($publishedAt);
            if ($pinned) {
                $job->pinUntil(new \DateTimeImmutable('+1 month'));
            }

            $manager->persist($job);
        }

        $manager->flush();
    }

    private function loadEvents(ObjectManager $manager): void
    {
        foreach ($this->getEventData() as [$id, $name, $startDate, $endDate, $location, $abstract, $url, $countryCode, $attendanceMode]) {
            $event = new Event($id ? Uuid::fromString($id) : null);
            $event->setName($name);
            $event->setUrl($url);
            $event->setAbstract($abstract);
            $event->setLocation($location);
            $event->setStartDate(\DateTimeImmutable::createFromFormat('Y-m-d', $startDate));
            $event->setEndDate(\DateTimeImmutable::createFromFormat('Y-m-d', $endDate));
            $event->setCountry($countryCode);
            $event->setAttendanceMode($attendanceMode);

            $manager->persist($event);
        }

        $manager->flush();
    }

    private function loadNews(ObjectManager $manager): void
    {
        $feedRSS = new Feed(Uuid::fromString(self::FEED_RSS_ID));
        $feedRSS->setName('RSS Feed');
        $feedRSS->setUrl('https://localhost/rss');
        $feedRSS->setType(FeedType::RSS);
        $manager->persist($feedRSS);
        $this->addReference(sprintf('feed-%s', self::FEED_RSS_ID), $feedRSS);

        $feedAtom = new Feed(Uuid::fromString(self::FEED_ATOM_ID));
        $feedAtom->setName('Atom Feed');
        $feedAtom->setUrl('https://localhost/atom');
        $feedAtom->setType(FeedType::ATOM);
        $manager->persist($feedAtom);
        $this->addReference(sprintf('feed-%s', self::FEED_ATOM_ID), $feedAtom);

        foreach ($this->getNewsData() as [$id, $title, $link, $description, $publishedAt, $feedId]) {
            /** @var Feed $feed */
            $feed = $this->getReference(sprintf('feed-%s', $feedId));

            $article = new Entry($id ? Uuid::fromString($id) : null);
            $article->setFeed($feed);
            $article->setTitle($title);
            $article->setLink($link);
            $article->setDescription($description);
            $article->setPublishedAt(\DateTimeImmutable::createFromFormat('Y-m-d', $publishedAt));

            $manager->persist($article);
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
            AttendanceMode::OFFLINE,
        ];

        yield [
            null,
            'API Platform Conference 2022',
            '2022-09-15',
            '2022-09-16',
            'Lille',
            'The 2nd edition of the API Platform Conference (a popular open source framework for building hypermedia and GraphQL APIs ) is coming!',
            'https://api-platform.com/con/2022/',
            'FR',
            AttendanceMode::MIXED,
        ];

        yield [
            null,
            'SymfonyWorld Online 2022 Winter Edition',
            '2022-12-08',
            '2022-12-09',
            null,
            'Join us for the fifth edition of the international online SymfonyWorld conference. The entire conference will take place online during 4 days in English.',
            'https://live.symfony.com/2022-world-winter/',
            'FR',
            AttendanceMode::ONLINE,
        ];
    }

    private function getNewsData(): \Generator
    {
        yield [
            self::NEWS_1_ID,
            'Write your first tests',
            'https://localhost/write-first-tests',
            'Ready to write your first tests? Use PHPUnit on your Symfony app',
            '2022-11-03',
            self::FEED_RSS_ID,
        ];

        yield [
            null,
            'Why you should migrate your Symfony configs to PHP',
            'https://localhost/php-config',
            '<p>Yesterday, I had a quick discussion on Slack in the Symfony Support channel where somebody was asking about splitting up their services.yaml file into multiple included files.</p>',
            '2022-11-03',
            self::FEED_ATOM_ID,
        ];
    }
}
