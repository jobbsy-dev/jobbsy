<?php

namespace App\Tests\Job\WelcometotheJungle;

use App\Job\Scraping\JobScraper;
use App\Job\WelcometotheJungle\WelcometotheJungleClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class WelcometotheJungleClientTest extends TestCase
{
    public function test_crawl(): void
    {
        // Arrange
        /** @var string $body */
        $body = file_get_contents(__DIR__.'/data/wttj_list.html');
        $mockResponseList = new MockResponse($body);

        $httpClient1 = new MockHttpClient([$mockResponseList]);
        $goutteClient1 = new HttpBrowser($httpClient1);

        /** @var string $bodyJob1 */
        $bodyJob1 = file_get_contents(__DIR__.'/data/wttj_job1.html');
        $mockResponseJob1 = new MockResponse($bodyJob1);
        /** @var string $bodyJob2 */
        $bodyJob2 = file_get_contents(__DIR__.'/data/wttj_job2.html');
        $mockResponseJob2 = new MockResponse($bodyJob2);
        $httpClient2 = new MockHttpClient([
            $mockResponseJob1,
            $mockResponseJob2,
        ]);
        $goutteClient2 = new HttpBrowser($httpClient2);
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
