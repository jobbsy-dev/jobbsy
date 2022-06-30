<?php

namespace App\Broadcast\Twitter;

use App\Entity\Job;

final class JobPostedTweet
{
    public function __construct(
        private readonly Job $job,
        private readonly string $jobUrl
    ) {
    }

    public function toTweet(): Tweet
    {
        $hashtags = array_map(static function (string $tag) {
            return '#'.$tag;
        }, $this->job->getTags());

        $text = sprintf(
            "💻 %s\n🎸 %s\n👉 %s\n\n%s",
            $this->job->getTitle(),
            $this->job->getOrganization(),
            $this->jobUrl,
            implode(' ', $hashtags),
        );

        return new Tweet($text);
    }
}
