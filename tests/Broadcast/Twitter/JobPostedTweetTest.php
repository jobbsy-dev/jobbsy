<?php

namespace App\Tests\Broadcast\Twitter;

use App\Broadcast\Twitter\JobPostedTweet;
use App\Entity\Job;
use App\Job\EmploymentType;
use PHPUnit\Framework\TestCase;

final class JobPostedTweetTest extends TestCase
{
    public function testToTweetText(): void
    {
        // Arrange
        $job = new Job(
            'Symfony web developer',
            'Remote',
            EmploymentType::FULLTIME,
            'Acme',
            ''
        );
        $job->setTags(['PHP', 'Symfony']);

        $expectedTweetText = <<<EOD
        💻 Symfony web developer
        🎸 Acme
        📍 Remote
        👉 https://example.com/1

        #PHP #Symfony
        EOD;

        // Act
        $tweet = (new JobPostedTweet($job, 'https://example.com/1'))->toTweet();

        // Assert
        $this->assertSame($expectedTweetText, $tweet->text);
    }
}
