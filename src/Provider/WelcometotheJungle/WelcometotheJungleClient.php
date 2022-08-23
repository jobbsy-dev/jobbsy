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

                if (isset($decodedData['content']['@type']) && 'JobPosting' === $decodedData['content']['@type']) {
                    $structuredData = $decodedData['content'];

                    break;
                }
            }

            if (null === $structuredData) {
                continue;
            }

            $location = null;
            if (isset($structuredData['jobLocation']['@type']) && 'Place' === $structuredData['jobLocation']['@type']) {
                $location = sprintf(
                    '%s, %s',
                    $structuredData['jobLocation']['address']['addressLocality'],
                    ucfirst(Countries::getName($structuredData['jobLocation']['address']['addressCountry'])),
                );
            }

            $data[] = [
                'company' => trim($structuredData['hiringOrganization']['name']),
                'companyLogo' => $structuredData['hiringOrganization']['logo'],
                'url' => $url,
                'title' => html_entity_decode($structuredData['title']),
                'contractType' => $structuredData['employmentType'],
                'location' => $location,
            ];
        }

        return $data;
    }
}
