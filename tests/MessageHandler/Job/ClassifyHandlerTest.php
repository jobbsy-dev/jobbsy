<?php

namespace App\Tests\MessageHandler\Job;

use App\Entity\Job;
use App\Job\EmploymentType;
use App\Message\Job\ClassifyMessage;
use App\MessageHandler\Job\ClassifyHandler;
use App\OpenAI\Client;
use App\Tests\Repository\InMemoryJobRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class ClassifyHandlerTest extends TestCase
{
    public function testClassifyJob(): void
    {
        // Arrange
        $job = new Job('', '', EmploymentType::FULLTIME, '', '', Uuid::fromString('d43b7e10-cbc7-40d1-a9d4-aa73fc825456'));
        $job->setDescription('Amazing job for Symfony developer with AI skills');

        $payload = [
            'choices' => [
                [
                    'text' => 'Symfony, AI',
                ],
            ],
        ];
        $mockResponse = new MockResponse(json_encode($payload, \JSON_THROW_ON_ERROR));
        $httpClient = new MockHttpClient([$mockResponse]);
        $client = new Client($httpClient);

        $repository = new InMemoryJobRepository([$job]);

        $handler = new ClassifyHandler($client, $repository, 'model', new NullLogger());
        $message = new ClassifyMessage('d43b7e10-cbc7-40d1-a9d4-aa73fc825456');

        // Act
        ($handler)($message);

        // Assert
        self::assertCount(2, $job->getTags());
        self::assertSame(['Symfony', 'AI'], $job->getTags());
    }
}
