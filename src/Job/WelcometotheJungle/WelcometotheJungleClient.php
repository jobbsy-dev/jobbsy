<?php

namespace App\Job\WelcometotheJungle;

use App\Job\Scraping\JobScraper;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Intl\Countries;

final readonly class WelcometotheJungleClient
{
    private const string URL = 'https://www.welcometothejungle.com/fr/pages/emploi-developpeur-symfony';

    public function __construct(private HttpBrowser $httpBrowser, private JobScraper $jobScraping)
    {
    }

    public function crawl(): array
    {
        $crawler = $this->httpBrowser->request('GET', self::URL);

        $urls = $crawler->filter('ol:nth-child(2) li header a')->each(static function (Crawler $crawler): string {
            return $crawler->link()->getUri();
        });

        $data = [];
        foreach ($urls as $url) {
            $jobData = $this->jobScraping->scrap($url);

            if ($this->shouldSkip($jobData)) {
                continue;
            }

            $location = $this->getLocation($jobData);

            $data[] = [
                'company' => html_entity_decode(trim((string) $jobData['hiringOrganization']['name'])),
                'companyLogo' => $jobData['hiringOrganization']['logo'] ?? null,
                'url' => $url,
                'title' => html_entity_decode((string) $jobData['title']),
                'employmentType' => $jobData['employmentType'],
                'location' => $location,
                'locationType' => $jobData['jobLocationType'] ?? null,
                'description' => $jobData['description'] ?? null,
                'industry' => $jobData['industry'] ?? null,
            ];
        }

        return $data;
    }

    private function shouldSkip(array $data): bool
    {
        return false === isset($data['jobLocation']);
    }

    private function getLocation(array $data): string
    {
        // Check is multidimensional array (e.g. array of jobLocation)
        if (false === isset($data['jobLocation']['@type'])) {
            $locations = [];
            foreach ($data['jobLocation'] as $jobLocation) {
                if (false === isset($jobLocation['@type'])) {
                    continue;
                }

                if ('Place' !== $jobLocation['@type']) {
                    continue;
                }

                $locations[] = \sprintf(
                    '%s, %s',
                    html_entity_decode((string) $jobLocation['address']['addressLocality']),
                    ucfirst(Countries::getName($jobLocation['address']['addressCountry'])),
                );
            }

            return implode(', ', $locations);
        }

        if ('Place' === $data['jobLocation']['@type']) {
            return \sprintf(
                '%s, %s',
                html_entity_decode((string) $data['jobLocation']['address']['addressLocality']),
                ucfirst(Countries::getName($data['jobLocation']['address']['addressCountry'])),
            );
        }

        throw new \Exception('Unable to retrieve location.');
    }
}
