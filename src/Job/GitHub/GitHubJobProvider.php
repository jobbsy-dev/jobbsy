<?php

namespace App\Job\GitHub;

use App\Entity\Job;
use App\Job\EmploymentType;
use App\Job\JobCollection;
use App\Job\JobProviderInterface;
use App\Job\SearchParameters;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class GitHubJobProvider implements JobProviderInterface
{
    public function __construct(private HttpClientInterface $githubClient)
    {
    }

    public function retrieve(SearchParameters $parameters): JobCollection
    {
        $response = $this->githubClient->request(
            method: 'GET',
            url: '/repos/jlondiche/job-board-php/contents/',
            options: [
                'headers' => [
                    'Accept' => 'application/vnd.github.object',
                    'X-GitHub-Api-Version' => '2022-11-28',
                ],
            ]
        );

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Unable to retrieve jobs from GitHub.');
        }

        $jobs = [];
        $data = $response->toArray();
        foreach ($data['entries'] ?? [] as $entry) {
            if ('file' !== $entry['type']) {
                continue;
            }

            if (false === str_contains((string) $entry['name'], 'md')) {
                continue;
            }

            $markdown = file_get_contents($entry['download_url']);
            if (false === preg_match_all('/^# (.*)$/m', $markdown, $matches)) {
                continue;
            }

            $location = 'France';
            if (preg_match_all('/\*\*Où\s*:\*\*\s*(.*)/u', $markdown, $matchesWhere)) {
                $location = $matchesWhere[1][0];
            }

            $companyName = $matches[1][0];
            unset($matches[1][0]);

            foreach ($matches[1] as $title) {
                if (false === str_contains($title, 'Symfony')) {
                    continue;
                }

                $job = new Job(
                    title: $title,
                    location: $location,
                    employmentType: EmploymentType::FULLTIME,
                    organization: $companyName,
                    url: $entry['html_url'],
                );
                $job->setSource('github');
                $job->publish();

                $jobs[] = $job;
            }
        }

        return new JobCollection(...$jobs);
    }

    public function enabled(): bool
    {
        return true;
    }
}
