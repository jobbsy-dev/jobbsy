<?php

namespace App\MessageHandler\Job;

use App\Job\Bridge\OpenAI\CreateJobPromptForClassification;
use App\Message\Job\ClassifyMessage;
use App\OpenAI\Client;
use App\OpenAI\Model\CompletionRequest;
use App\Repository\JobRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ClassifyHandler
{
    public function __construct(
        private Client $openAIClient,
        private JobRepository $jobRepository,
        #[Autowire('%env(OPENAI_API_COMPLETION_MODEL)%')]
        private string $model
    ) {
    }

    public function __invoke(ClassifyMessage $message): void
    {
        $job = $this->jobRepository->find($message->jobId);

        if (null === $job) {
            return;
        }

        $prompt = CreateJobPromptForClassification::create($job);
        $result = $this->openAIClient->completions(new CompletionRequest($this->model, $prompt, 0.5));

        if (false === isset($result['choices'][0]['text'])) {
            return;
        }

        $keywords = array_filter(array_map('trim', explode(',', $result['choices'][0]['text'])));

        $job->setTags($keywords);

        $this->jobRepository->save($job, true);
    }
}
