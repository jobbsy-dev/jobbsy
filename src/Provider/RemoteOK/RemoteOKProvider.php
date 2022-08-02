<?php

namespace App\Provider\RemoteOK;

use App\Entity\Job;
use App\Provider\JobCollection;
use App\Provider\JobProviderInterface;
use App\Provider\SearchParameters;

final class RemoteOKProvider implements JobProviderInterface
{
    public const SOURCE_NAME = 'RemoteOK';

    public function __construct(private readonly RemoteOKApi $remoteOKApi)
    {
    }

    public function retrieve(SearchParameters $parameters): JobCollection
    {
        $jobCollection = new JobCollection();

        $queryParams = [
            'tags' => 'symfony',
        ];

        $results = $this->remoteOKApi->search($queryParams);

        foreach ($results as $result) {
            if (false === isset($result['position'], $result['location'], $result['company'], $result['url'])) {
                continue;
            }

            $job = new Job();
            $job->setLocation($result['location']);
            $job->setTitle($result['position']);
            $job->setOrganization($result['company']);
            $job->setOrganizationImageUrl($result['logo'] ?? null);
            $job->setSource(self::SOURCE_NAME);
            $job->setTags(isset($result['tags']) ? \array_slice($result['tags'], 0, 5) : []);
            $job->setUrl($result['url']);

            $jobCollection->addJob($job);
        }

        return $jobCollection;
    }

    public function enabled(): bool
    {
        return true;
    }
}
