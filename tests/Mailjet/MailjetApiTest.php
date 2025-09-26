<?php

namespace App\Tests\Mailjet;

use App\Mailjet\MailjetApi;
use App\Mailjet\Model\CreateCampaignDraft\CreateCampaignDraftRequest;
use App\Mailjet\Model\CreateCampaignDraftContent\CreateCampaignDraftContentRequest;
use App\Mailjet\Model\ManageContact\Action;
use App\Mailjet\Model\ManageContact\ManageContactRequest;
use App\Mailjet\Model\SendCampaignDraft\SendCampaignDraftRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class MailjetApiTest extends TestCase
{
    public function test_create_contact(): void
    {
        // Arrange
        $requestData = [
            'Email' => 'john@example.com',
        ];
        $expectedRequestData = json_encode($requestData, \JSON_THROW_ON_ERROR);

        $expectedResponseData = [
            'Count' => 1,
            'Data' => [
                ['ID' => 42],
            ],
            'Total' => 1,
        ];
        $mockResponseJson = json_encode($expectedResponseData, \JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 201,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($mockResponse, 'https://example.com');

        $mailjetApi = new MailjetApi($httpClient);

        // Act
        $responseData = $mailjetApi->createContact('john@example.com');

        // Assert
        self::assertSame('POST', $mockResponse->getRequestMethod());
        self::assertSame('https://example.com/contact', $mockResponse->getRequestUrl());
        $this->assertArrayHasKey('headers', $mockResponse->getRequestOptions());
        $this->assertIsArray($mockResponse->getRequestOptions()['headers']);
        self::assertContains(
            'Content-Type: application/json',
            $mockResponse->getRequestOptions()['headers']
        );
        self::assertSame($expectedRequestData, $mockResponse->getRequestOptions()['body']);
        self::assertSame($expectedResponseData['Data'], $responseData);
    }

    public function test_add_contact_to_list(): void
    {
        // Arrange
        $requestData = [
            'ContactID' => '123',
            'ListID' => '456',
        ];
        $expectedRequestData = json_encode($requestData, \JSON_THROW_ON_ERROR);

        $expectedResponseData = [
            'Count' => 1,
            'Data' => [
                ['ID' => 42],
            ],
            'Total' => 1,
        ];
        $mockResponseJson = json_encode($expectedResponseData, \JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 201,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($mockResponse, 'https://example.com');

        $mailjetApi = new MailjetApi($httpClient);

        // Act
        $responseData = $mailjetApi->addContactToList('123', '456');

        // Assert
        self::assertSame('POST', $mockResponse->getRequestMethod());
        self::assertSame('https://example.com/listrecipient', $mockResponse->getRequestUrl());
        $this->assertArrayHasKey('headers', $mockResponse->getRequestOptions());
        $this->assertIsArray($mockResponse->getRequestOptions()['headers']);
        self::assertContains(
            'Content-Type: application/json',
            $mockResponse->getRequestOptions()['headers']
        );
        self::assertSame($expectedRequestData, $mockResponse->getRequestOptions()['body']);
        self::assertSame($expectedResponseData['Data'], $responseData);
    }

    public function test_create_campaign_draft(): void
    {
        // Arrange
        $requestData = [
            'Title' => 'My title',
            'ContactsListID' => 42,
            'Locale' => 'en_US',
            'SenderEmail' => 'hello@example.com',
            'SenderName' => 'John Doe',
            'Subject' => 'Hello World!',
            'Sender' => '99',
        ];
        $expectedRequestData = json_encode($requestData, \JSON_THROW_ON_ERROR);

        $expectedResponseData = [
            'Count' => 1,
            'Data' => [
                ['ID' => 65],
            ],
            'Total' => 1,
        ];
        $mockResponseJson = json_encode($expectedResponseData, \JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 201,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($mockResponse, 'https://example.com');

        $mailjetApi = new MailjetApi($httpClient);

        // Act
        $response = $mailjetApi->createCampaignDraft(new CreateCampaignDraftRequest(
            'My title',
            42,
            'en_US',
            'hello@example.com',
            'John Doe',
            'Hello World!',
            '99'
        ));

        // Assert
        self::assertSame('POST', $mockResponse->getRequestMethod());
        self::assertSame('https://example.com/campaigndraft', $mockResponse->getRequestUrl());
        $this->assertArrayHasKey('headers', $mockResponse->getRequestOptions());
        $this->assertIsArray($mockResponse->getRequestOptions()['headers']);
        self::assertContains(
            'Content-Type: application/json',
            $mockResponse->getRequestOptions()['headers']
        );
        self::assertSame($expectedRequestData, $mockResponse->getRequestOptions()['body']);
        self::assertNotNull($response);
        self::assertSame(65, $response->data[0]['ID']);
    }

    public function test_create_campaign_draft_content(): void
    {
        // Arrange
        $requestData = [
            'Html-part' => '<h1>My title</h1>',
        ];
        $expectedRequestData = json_encode($requestData, \JSON_THROW_ON_ERROR);

        $expectedResponseData = [
            'Count' => 1,
            'Data' => [
                ['Html-part' => '<h1>My title</h1>'],
            ],
            'Total' => 1,
        ];
        $mockResponseJson = json_encode($expectedResponseData, \JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 201,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($mockResponse, 'https://example.com');

        $mailjetApi = new MailjetApi($httpClient);

        // Act
        $response = $mailjetApi->createCampaignDraftContent(new CreateCampaignDraftContentRequest(
            1,
            '<h1>My title</h1>',
        ));

        // Assert
        self::assertSame('POST', $mockResponse->getRequestMethod());
        self::assertSame('https://example.com/campaigndraft/1/detailcontent', $mockResponse->getRequestUrl());
        self::assertContains(
            'Content-Type: application/json',
            $mockResponse->getRequestOptions()['headers']
        );
        self::assertSame($expectedRequestData, $mockResponse->getRequestOptions()['body']);
        self::assertNotNull($response);
        self::assertSame('<h1>My title</h1>', $response->data[0]['Html-part']);
    }

    public function test_send_campaign_draft(): void
    {
        // Arrange
        $expectedResponseData = [
            'Count' => 1,
            'Data' => [
                ['Status' => 'Programmed'],
            ],
            'Total' => 1,
        ];
        $mockResponseJson = json_encode($expectedResponseData, \JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 201,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($mockResponse, 'https://example.com');

        $mailjetApi = new MailjetApi($httpClient);

        // Act
        $response = $mailjetApi->sendCampaignDraft(new SendCampaignDraftRequest(1));

        // Assert
        self::assertSame('POST', $mockResponse->getRequestMethod());
        self::assertSame('https://example.com/campaigndraft/1/send', $mockResponse->getRequestUrl());
        $this->assertNotNull($response);
        self::assertSame($expectedResponseData['Data'], $response->data);
        self::assertNotNull($response);
        self::assertSame('Programmed', $response->data[0]['Status']);
    }

    public function test_manage_contact(): void
    {
        // Arrange
        $requestData = [
            'Action' => 'addforce',
            'Email' => 'test@example.com',
        ];
        $expectedRequestData = json_encode($requestData, \JSON_THROW_ON_ERROR);

        $expectedResponseData = [
            'Count' => 1,
            'Data' => [
                [
                    'Action' => 'addforce',
                    'Email' => 'test@example.com',
                ],
            ],
            'Total' => 1,
        ];
        $mockResponseJson = json_encode($expectedResponseData, \JSON_THROW_ON_ERROR);

        $mockResponse = new MockResponse($mockResponseJson, [
            'http_code' => 201,
            'response_headers' => ['Content-Type: application/json'],
        ]);
        $httpClient = new MockHttpClient($mockResponse, 'https://example.com');

        $mailjetApi = new MailjetApi($httpClient);

        // Act
        $response = $mailjetApi->manageContact(new ManageContactRequest(
            1,
            Action::ADD_FORCE,
            'test@example.com'
        ));

        // Assert
        self::assertSame('POST', $mockResponse->getRequestMethod());
        self::assertSame('https://example.com/contactslist/1/managecontact', $mockResponse->getRequestUrl());
        self::assertContains(
            'Content-Type: application/json',
            $mockResponse->getRequestOptions()['headers']
        );
        self::assertSame($expectedRequestData, $mockResponse->getRequestOptions()['body']);
        self::assertNotNull($response);
        self::assertSame('test@example.com', $response->data[0]['Email']);
    }
}
