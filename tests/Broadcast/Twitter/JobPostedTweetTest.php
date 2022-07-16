<?php

namespace App\Tests\Broadcast\Twitter;

use App\Broadcast\Twitter\JobPostedTweet;
use App\Entity\Job;
use PHPUnit\Framework\TestCase;

class JobPostedTweetTest extends TestCase
{
    public function testToTweetText(): void
    {
        // Arrange
        $job = new Job();
        $job->setTitle('Symfony web developer');
        $job->setOrganization('Acme');
        $job->setLocation('Remote');
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
