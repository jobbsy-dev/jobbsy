<?php

namespace App\Tests\Provider\WelcometotheJungle;

use App\Provider\Scraping\JobScraper;
use App\Provider\SearchParameters;
use App\Provider\WelcometotheJungle\WelcometotheJungleClient;
use App\Provider\WelcometotheJungle\WelcometotheJungleProvider;
use Goutte\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class WelcometotheJungleProviderTest extends TestCase
{
    public function testRetrieve(): void
    {
        // Arrange
        $mockResponseList = new MockResponse(file_get_contents(__DIR__.'/data/wttj_list.html'));

        $httpClient1 = new MockHttpClient([$mockResponseList]);
        $goutteClient1 = new Client($httpClient1);

        $mockResponseJob1 = new MockResponse(file_get_contents(__DIR__.'/data/wttj_job1.html'));
        $mockResponseJob2 = new MockResponse(file_get_contents(__DIR__.'/data/wttj_job2.html'));
        $httpClient2 = new MockHttpClient([
            $mockResponseJob1,
            $mockResponseJob2,
        ]);
        $goutteClient2 = new Client($httpClient2);
        $jobScraping = new JobScraper($goutteClient2);

        $client = new WelcometotheJungleClient($goutteClient1, $jobScraping);
        $provider = new WelcometotheJungleProvider($client);

        // Act
        $jobCollection = $provider->retrieve(new SearchParameters());

        // Assert
        self::assertCount(2, $jobCollection->all());
        self::assertSame('Développeur Symfony en télétravail F/H', $jobCollection->all()[0]->getTitle());
        self::assertSame('AddixGroup', $jobCollection->all()[0]->getOrganization());
        self::assertSame('Sophia Antipolis, France', $jobCollection->all()[0]->getLocation());
    }
}
