<?php

namespace App\Tests\Job\PoleEmploi;

use App\Job\PoleEmploi\PoleEmploiApi;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class PoleEmploiApiTest extends TestCase
{
    public function test_search_with_is_o8601_date_time_format(): void
    {
        // Arrange
        $minCreationDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2022-09-01 10:00:00')
            ->setTimezone(new \DateTimeZone('UTC'));
        $maxCreationDate = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2022-09-02 10:00:00')
            ->setTimezone(new \DateTimeZone('UTC'));
        $expectedSearchUrl = sprintf(
            'https://api.francetravail.io/partenaire/offresdemploi/v2/offres/search?minCreationDate=%s&maxCreationDate=%s',
            '2022-09-01T10:00:00Z',
            '2022-09-02T10:00:00Z'
        );
        $mockResponseSearch = new MockResponse(json_encode(['ok'], \JSON_THROW_ON_ERROR));

        $client = new MockHttpClient([$mockResponseSearch]);
        $poleEmploiApi = new PoleEmploiApi('clientId', 'clientSecret', $client);

        // Act
        $poleEmploiApi->search([
            'minCreationDate' => $minCreationDate,
            'maxCreationDate' => $maxCreationDate,
        ]);

        // Assert
        self::assertSame($expectedSearchUrl, $mockResponseSearch->getRequestUrl());
    }
}
