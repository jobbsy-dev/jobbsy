<?php

namespace App\Broadcast\Twitter;

use App\Entity\Job;

final readonly class JobPostedTweet
{
    public function __construct(
        private Job $job,
        private string $jobUrl
    ) {
    }

    public function toTweet(): Tweet
    {
        $hashtags = array_map(static function (string $tag) {
            return '#'.$tag;
        }, $this->job->getTags());

        $text = sprintf(
            "💻 %s\n🎸 %s\n📍 %s\n👉 %s\n\n%s",
            $this->job->getTitle(),
            $this->job->getOrganization(),
            $this->job->getLocation(),
            $this->jobUrl,
            implode(' ', $hashtags),
        );

        return new Tweet($text);
    }
}
