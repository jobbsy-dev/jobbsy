<?php

namespace App\Job\EventSubscriber;

use App\Job\Event\JobPostedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

final readonly class NotifyNewJobOfferSubscriber implements EventSubscriberInterface
{
    public function __construct(private ChatterInterface $chatter)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            JobPostedEvent::class => 'onJobPosted',
        ];
    }

    public function onJobPosted(JobPostedEvent $event): void
    {
        $job = $event->job;

        if (false === $job->isManualPublishing()) {
            return;
        }

        $jobTitleText = sprintf("New job offer posted:\n*%s*", $job->getTitle());
        $jobTitleSection = (new SlackSectionBlock())
            ->text($jobTitleText);

        $detailsSection = (new SlackSectionBlock())
            ->field('Organization')
            ->field($job->getOrganization())
            ->field('Location')
            ->field($job->getLocation())
            ->field('Type')
            ->field($job->getEmploymentType()->value)
            ->field('Url')
            ->field($job->getUrl())
        ;

        if (false === empty($job->getSalary())) {
            $detailsSection
                ->field('Salary')
                ->field($job->getSalary());
        }

        $slackOptions = (new SlackOptions())
            ->block($jobTitleSection)
            ->block($detailsSection)
        ;

        $message = (new ChatMessage('New job posted', $slackOptions))
            ->transport('slack');

        $this->chatter->send($message);
    }
}
