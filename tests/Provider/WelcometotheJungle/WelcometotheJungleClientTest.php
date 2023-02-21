<?php

namespace App\Tests\Provider\WelcometotheJungle;

use App\Provider\Scraping\JobScraper;
use App\Provider\WelcometotheJungle\WelcometotheJungleClient;
use Goutte\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class WelcometotheJungleClientTest extends TestCase
{
    public function testCrawl(): void
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

        // Act
        $data = $client->crawl();

        // Assert
        self::assertCount(2, $data);
        self::assertSame('AddixGroup', $data[0]['company']);
        self::assertSame('Développeur Symfony en télétravail F/H', $data[0]['title']);
        self::assertSame('Sophia Antipolis, France', $data[0]['location']);

        self::assertSame("L'olivier Assurance", $data[1]['company']);
        self::assertSame('Développeur Symfony', $data[1]['title']);
        self::assertSame('Paris, France', $data[1]['location']);
    }
}
