<?php

namespace App\Tests\Provider\Arbeitsagentur;

use App\Provider\Arbeitsagentur\ArbeitsagenturApi;
use App\Provider\Arbeitsagentur\ArbeitsagenturJobProvider;
use App\Provider\SearchParameters;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ArbeitsagenturJobProviderTest extends TestCase
{
    public function testRetrieve(): void
    {
        // Arrange
        $requestDataAuthentication = [
            'grant_type' => 'client_credentials',
            'client_id' => 'clientId',
            'client_secret' => 'clientSecret',
        ];
        $expectedRequestDataAuthentication = http_build_query($requestDataAuthentication);

        $expectedResponseDataAuthentication = ['access_token' => '12345', 'expires_in' => 999999];
        $mockResponseJsonAuthentication = json_encode($expectedResponseDataAuthentication, \JSON_THROW_ON_ERROR);
        $mockResponseAuthenticate = new MockResponse($mockResponseJsonAuthentication);

        $expectedResponseData = [
            'stellenangebote' => [
                [
                    'titel' => 'Developer Symfony',
                    'arbeitgeber' => 'Acme',
                    'externeUrl' => 'https://example.com',
                    'arbeitsort' => [
                        'ort' => 'Remote',
                    ],
                ],
                [
                    'intitule' => 'Developer React',
                ],
            ],
        ];
        $mockResponseJson = json_encode($expectedResponseData, \JSON_THROW_ON_ERROR);
        $mockResponseSearch = new MockResponse($mockResponseJson);

        $client = new MockHttpClient([
            $mockResponseAuthenticate,
            $mockResponseSearch,
        ]);
        $api = new ArbeitsagenturApi(
            'clientId', 'clientSecret', $client
        );
        $filesystem = new Filesystem(new InMemoryFilesystemAdapter());
        $poleEmploiProvider = new ArbeitsagenturJobProvider(
            $api,
            $filesystem
        );

        // Act
        $jobCollection = $poleEmploiProvider->retrieve(new SearchParameters());

        // Assert
        self::assertSame($expectedRequestDataAuthentication, $mockResponseAuthenticate->getRequestOptions()['body']);
        self::assertCount(1, $jobCollection->all());
        self::assertSame('Developer Symfony', $jobCollection->all()[0]->getTitle());
        self::assertSame('Acme', $jobCollection->all()[0]->getOrganization());
        self::assertSame('Remote', $jobCollection->all()[0]->getLocation());
    }
}
