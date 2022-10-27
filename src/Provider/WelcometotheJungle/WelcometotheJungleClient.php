<?php

namespace App\Provider\WelcometotheJungle;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Intl\Countries;

final class WelcometotheJungleClient
{
    private Client $goutteClient;

    public function __construct(?Client $goutteClient = null)
    {
        if (null === $goutteClient) {
            $goutteClient = new Client();
        }

        $this->goutteClient = $goutteClient;
    }

    public function crawl(): array
    {
        $crawler = $this->goutteClient->request(
            'GET',
            'https://www.welcometothejungle.com/fr/pages/emploi-developpeur-symfony'
        );

        $urls = $crawler->filter('ol:nth-child(2) > li header a')->each(function (Crawler $crawler) {
            return $crawler->link()->getUri();
        });

        $data = [];
        foreach ($urls as $url) {
            $crawler = $this->goutteClient->request('GET', $url);

            $structuredData = null;
            foreach ($crawler->filter('script[type="application/ld+json"]') as $domElement) {
                $decodedData = json_decode($domElement->textContent, true, 512, \JSON_THROW_ON_ERROR);

                if (isset($decodedData['@type']) && 'JobPosting' === $decodedData['@type']) {
                    $structuredData = $decodedData;

                    break;
                }
            }

            if (null === $structuredData) {
                continue;
            }

            $location = null;
            if (isset($structuredData['jobLocation'])) {
                // Check is multidimensional array (e.g. array of jobLocation)
                if (false === isset($structuredData['jobLocation']['@type'])) {
                    $locations = [];
                    foreach ($structuredData['jobLocation'] as $jobLocation) {
                        if (false === isset($jobLocation['@type'])) {
                            continue;
                        }

                        if ('Place' !== $jobLocation['@type']) {
                            continue;
                        }

                        $locations[] = sprintf(
                            '%s, %s',
                            html_entity_decode($jobLocation['address']['addressLocality']),
                            ucfirst(Countries::getName($jobLocation['address']['addressCountry'])),
                        );
                    }

                    $location = implode(', ', $locations);
                } elseif ('Place' === $structuredData['jobLocation']['@type']) {
                    $location = sprintf(
                        '%s, %s',
                        html_entity_decode($structuredData['jobLocation']['address']['addressLocality']),
                        ucfirst(Countries::getName($structuredData['jobLocation']['address']['addressCountry'])),
                    );
                }
            }

            if (null === $location) {
                continue;
            }

            $data[] = [
                'company' => html_entity_decode(trim($structuredData['hiringOrganization']['name'])),
                'companyLogo' => $structuredData['hiringOrganization']['logo'],
                'url' => $url,
                'title' => html_entity_decode($structuredData['title']),
                'employmentType' => $structuredData['employmentType'],
                'location' => $location,
            ];
        }

        return $data;
    }
}
