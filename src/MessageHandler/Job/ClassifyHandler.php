<?php

namespace App\MessageHandler\Job;

use App\Job\Bridge\OpenAI\CreateJobPromptForClassification;
use App\Job\Repository\JobRepositoryInterface;
use App\Message\Job\ClassifyMessage;
use App\OpenAI\Client;
use App\OpenAI\Model\CompletionRequest;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ClassifyHandler
{
    public function __construct(
        private Client $openAIClient,
        private JobRepositoryInterface $jobRepository,
        #[Autowire('%env(OPENAI_API_COMPLETION_MODEL)%')] private string $model
    ) {
    }

    public function __invoke(ClassifyMessage $message): void
    {
        $job = $this->jobRepository->get(Uuid::fromString($message->jobId));

        $prompt = CreateJobPromptForClassification::create($job);
        $result = $this->openAIClient->completions(new CompletionRequest($this->model, $prompt, 0.8, 30));

        if (false === isset($result['choices'][0]['text'])) {
            return;
        }

        $keywords = array_filter(array_map('trim', explode(',', (string) $result['choices'][0]['text'])));

        $job->setTags(\array_slice($keywords, 0, 5));

        $this->jobRepository->save($job, true);
    }
}
