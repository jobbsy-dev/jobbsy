<?php

namespace App\Tests\Broadcast\Twitter;

use App\Broadcast\Twitter\JobPostedTweet;
use App\Entity\Job;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class JobPostedTweetTest extends TestCase
{
    public function testToTweetText(): void
    {
        // Arrange
        $job = new Job(Uuid::fromString('c018222a-ec42-4252-b795-31b34c589ce1'));
        $job->setTitle('Symfony web developer');
        $job->setOrganization('Acme');
        $job->setTags(['PHP', 'Symfony']);

        $expectedTweetText = <<<EOD
        💻 Symfony web developer
        🎸 Acme
        👉 https://example.com/1

        #PHP #Symfony
        EOD;

        // Act
        $tweet = (new JobPostedTweet($job, 'https://example.com/1'))->toTweet();

        // Assert
        $this->assertSame($expectedTweetText, $tweet->text);
    }
}
