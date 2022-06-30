<?php

namespace App\MessageHandler;

use App\Broadcast\Twitter\JobPostedTweet;
use App\Broadcast\Twitter\TwitterApi;
use App\Message\CreateTweetMessage;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CreateTweetMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly JobRepository $jobRepository,
        private readonly TwitterApi $twitterApi,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function __invoke(CreateTweetMessage $message): void
    {
        $job = $this->jobRepository->find($message->jobId);

        if (null === $job) {
            return;
        }

        $jobPostedTweet = new JobPostedTweet($job, $message->jobUrl);

        $tweetId = $this->twitterApi->createTweet($jobPostedTweet->toTweet());
        $job->setTweetId($tweetId);

        $this->em->flush();
    }
}
