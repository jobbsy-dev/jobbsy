<?php

namespace App\Tests\Job\FranceTravail;

use App\Job\FranceTravail\FranceTravailApi;
use App\Job\FranceTravail\FranceTravailJobProvider;
use App\Job\SearchParameters;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class FranceTravailJobProviderTest extends TestCase
{
    public function test_retrieve(): void
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
                        'logo' => 'https://example.com/logo',
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
                [
                    'intitule' => 'Développeur Front Angular H/F (H/F)',
                    'entreprise' => [
                        'nom' => 'Acme',
                        'logo' => 'https://example.com/logo',
                    ],
                    'origineOffre' => [
                        'urlOrigine' => 'https://example.com',
                    ],
                    'lieuTravail' => [
                        'libelle' => 'Remote',
                    ],
                ],
            ],
        ];
        $mockResponseJson = json_encode($expectedResponseData, \JSON_THROW_ON_ERROR);
        $mockResponseSearch = new MockResponse($mockResponseJson);

        $client = new MockHttpClient([
            $mockResponseAuthenticate,
            $mockResponseSearch,
        ]);
        $poleEmploiApi = new FranceTravailApi(
            poleEmploiClientId: 'clientId',
            poleEmploiClientSecret: 'clientSecret',
            httpClient: $client,
            logger: new NullLogger(),
        );
        $poleEmploiProvider = new FranceTravailJobProvider(
            $poleEmploiApi,
            'clientId',
            new NullLogger(),
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
