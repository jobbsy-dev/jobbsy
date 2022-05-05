<?php

namespace App\Tests\EventSubscriber;

use App\Entity\Job;
use App\Event\JobPostedEvent;
use App\EventSubscriber\CreateTweetSubscriber;
use App\Message\CreateTweetMessage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Symfony\Component\Uid\Uuid;

class CreateTweetSubscriberTest extends KernelTestCase
{
    public function testOnJobPosted(): void
    {
        self::bootKernel();
        $container = static::getContainer();

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

        $subscriber = $container->get(CreateTweetSubscriber::class);
        $subscriber->onJobPosted(new JobPostedEvent($job, 'https://example.com/1'));

        /* @var InMemoryTransport $transport */
        $transport = $container->get('messenger.transport.async');
        self::assertCount(1, $transport->getSent());

        /** @var CreateTweetMessage $message */
        $message = $transport->getSent()[0]->getMessage();
        self::assertSame($expectedTweetText, $message->text);
    }
}
