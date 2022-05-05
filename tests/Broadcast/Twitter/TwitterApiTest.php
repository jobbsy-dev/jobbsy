<?php

namespace App\Tests\Broadcast\Twitter;

use App\Broadcast\Twitter\Tweet;
use App\Broadcast\Twitter\TwitterApi;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class TwitterApiTest extends TestCase
{
    public function testCreateTweet(): void
    {
        // Arrange
        $requestData = ['text' => 'Hello World!'];
        $expectedRequestData = json_encode($requestData, \JSON_THROW_ON_ERROR);

        $expectedResponseData = [
            'data' => [
                'id' => '1234',
                'text' => 'Hello World!',
            ],
        ];
        $mockResponseJson = json_encode($expectedResponseData, \JSON_THROW_ON_ERROR);
        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 201,
            'response_headers' => ['Content-Type: application/json'],
        ]);

        $httpClient = new MockHttpClient($mockResponse);
        $api = new TwitterApi('xxx', 'xxx', 'xxx', 'xxx', $httpClient);

        // Act
        $responseData = $api->createTweet(new Tweet('Hello World!'));

        // Assert
        self::assertSame('POST', $mockResponse->getRequestMethod());
        self::assertSame('https://api.twitter.com/2/tweets', $mockResponse->getRequestUrl());

        self::assertContains(
            'Content-Type: application/json',
            $mockResponse->getRequestOptions()['headers']
        );

        self::assertSame($expectedRequestData, $mockResponse->getRequestOptions()['body']);
        self::assertSame($responseData, $expectedResponseData);
    }
}
