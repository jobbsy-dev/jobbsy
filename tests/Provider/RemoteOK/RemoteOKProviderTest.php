<?php

namespace App\Tests\Provider\RemoteOK;

use App\Provider\RemoteOK\RemoteOKApi;
use App\Provider\RemoteOK\RemoteOKProvider;
use App\Provider\SearchParameters;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class RemoteOKProviderTest extends TestCase
{
    public function testRetrieve(): void
    {
        // Arrange
        $expectedResponseData = [
            [
                'id' => '12346789',
                'company' => 'Remote OK',
                'position' => 'Senior Remote Symfony Developer',
                'location' => 'Worldwide',
                'url' => 'https://remoteok.com',
            ],
            [
                'id' => '23467891',
                'company' => 'Acme Inc',
            ],
        ];
        $mockResponseJson = json_encode($expectedResponseData, \JSON_THROW_ON_ERROR);
        $mockResponseSearch = new MockResponse($mockResponseJson);

        $client = new MockHttpClient([$mockResponseSearch]);
        $api = new RemoteOKApi($client);
        $provider = new RemoteOKProvider($api);

        // Act
        $jobCollection = $provider->retrieve(new SearchParameters());

        // Assert
        self::assertCount(1, $jobCollection->all());
        self::assertSame('Senior Remote Symfony Developer', $jobCollection->all()[0]->getTitle());
        self::assertSame('Remote OK', $jobCollection->all()[0]->getOrganization());
        self::assertSame('Worldwide', $jobCollection->all()[0]->getLocation());
        self::assertSame('https://remoteok.com', $jobCollection->all()[0]->getUrl());
    }
}
