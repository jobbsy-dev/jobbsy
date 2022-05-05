<?php

namespace App\MessageHandler;

use App\Broadcast\Twitter\Tweet;
use App\Broadcast\Twitter\TwitterApi;
use App\Message\CreateTweetMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CreateTweetMessageHandler implements MessageHandlerInterface
{
    public function __construct(private readonly TwitterApi $twitterApi)
    {
    }

    public function __invoke(CreateTweetMessage $message): void
    {
        $this->twitterApi->createTweet(new Tweet($message->text));
    }
}
