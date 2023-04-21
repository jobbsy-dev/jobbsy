<?php

namespace App\Job\Command;

use App\Entity\Job;
use App\Job\Event\JobPostedEvent;
use App\Job\Repository\JobRepositoryInterface;
use App\Media\MediaFactory;
use App\Media\MediaUploader;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class PostJobOfferCommandHandler
{
    public function __construct(
        private JobRepositoryInterface $jobRepository,
        private EventDispatcherInterface $eventDispatcher,
        private MediaUploader $mediaUploader,
        private MediaFactory $mediaFactory
    ) {
    }

    public function __invoke(PostJobOfferCommand $command): Job
    {
        $job = $command->toEntity();

        if (null !== $command->organizationImageFile) {
            $media = $this->mediaFactory->createFromUploadedFile($command->organizationImageFile);
            $job->changeOrganizationImage($media);
            $this->mediaUploader->upload($media);
        }

        $job->publish();

        // Allow 1 month of boost on manual creation
        $job->pinUntil(new \DateTimeImmutable('+1 month'));
        $this->jobRepository->save($job, true);

        $this->eventDispatcher->dispatch(new JobPostedEvent($job));

        return $job;
    }
}
