<?php

namespace App\Tests\Provider\PoleEmploi;

use App\Provider\PoleEmploi\PoleEmploiApi;
use App\Provider\PoleEmploi\PoleEmploiJobProvider;
use App\Provider\SearchParameters;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class PoleEmploiJobProviderTest extends TestCase
{
    public function testRetrieve(): void
    {
        // Arrange
        $requestDataAuthentication = [
            'grant_type' => 'client_credentials',
            'client_id' => 'clientId',
            'client_secret' => 'clientSecret',
            'scope' => implode(' ', ['api_offresdemploiv2', 'o2dsoffre', 'application_clientId']),
        ];
        $expectedRequestDataAuthentication = http_build_query($requestDataAuthentication);

        $expectedResponseDataAuthentication = ['access_token' => '12345', 'expires_in' => 999999];
        $mockResponseJsonAuthentication = json_encode($expectedResponseDataAuthentication, \JSON_THROW_ON_ERROR);
        $mockResponseAuthenticate = new MockResponse($mockResponseJsonAuthentication);

        $expectedResponseData = [
            'resultats' => [
                [
                    'intitule' => 'Développeur Symfony',
                    'entreprise' => [
                        'nom' => 'Acme',
                    ],
                    'origineOffre' => [
                        'urlOrigine' => 'https://example.com',
                    ],
                    'lieuTravail' => [
                        'libelle' => 'Remote',
                    ],
                ],
                [
                    'intitule' => 'Développeur React',
                ],
            ],
        ];
        $mockResponseJson = json_encode($expectedResponseData, \JSON_THROW_ON_ERROR);
        $mockResponseSearch = new MockResponse($mockResponseJson);

        $client = new MockHttpClient([
            $mockResponseAuthenticate,
            $mockResponseSearch,
        ]);
        $poleEmploiApi = new PoleEmploiApi(
            'clientId', 'clientSecret', $client
        );
        $poleEmploiProvider = new PoleEmploiJobProvider(
            $poleEmploiApi,
            'clientId'
        );

        // Act
        $jobCollection = $poleEmploiProvider->retrieve(new SearchParameters());

        // Assert
        self::assertSame($expectedRequestDataAuthentication, $mockResponseAuthenticate->getRequestOptions()['body']);
        self::assertCount(1, $jobCollection->all());
        self::assertSame('Développeur Symfony', $jobCollection->all()[0]->getTitle());
        self::assertSame('Acme', $jobCollection->all()[0]->getOrganization());
        self::assertSame('Remote', $jobCollection->all()[0]->getLocation());
    }
}
