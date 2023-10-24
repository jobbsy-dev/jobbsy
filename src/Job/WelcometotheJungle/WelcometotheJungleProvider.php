<?php

namespace App\Job\WelcometotheJungle;

use App\Entity\Job;
use App\Job\EmploymentType;
use App\Job\JobCollection;
use App\Job\JobProviderInterface;
use App\Job\LocationType;
use App\Job\SearchParameters;

final readonly class WelcometotheJungleProvider implements JobProviderInterface
{
    public const SOURCE_NAME = 'WTTJ';

    public function __construct(private WelcometotheJungleClient $client)
    {
    }

    public function retrieve(SearchParameters $parameters): JobCollection
    {
        $jobCollection = new JobCollection();

        $data = $this->client->crawl();

        foreach ($data as $datum) {
            $employmentType = null;
            switch ($datum['employmentType']) {
                case 'FULL_TIME':
                    $employmentType = EmploymentType::FULLTIME;
                    break;
                case 'CONTRACTOR':
                    $employmentType = EmploymentType::CONTRACT;
                    break;
                default:
                    continue 2;
            }

            $job = new Job(
                title: $datum['title'],
                location: $datum['location'],
                employmentType: $employmentType,
                organization: $datum['company'],
                url: $datum['url']
            );
            $job->setOrganizationImageUrl($datum['companyLogo'] ?? null);
            $job->setSource(self::SOURCE_NAME);
            $job->setTags(['PHP', 'Symfony']);
            $job->setIndustry($datum['industry']);
            $job->setDescription($datum['description']);

            if ('TELECOMMUTE' === $datum['locationType']) {
                $job->setLocationType(LocationType::REMOTE);
            }

            $job->publish();

            $jobCollection->addJob($job);
        }

        return $jobCollection;
    }

    public function enabled(): bool
    {
        return true;
    }
}
