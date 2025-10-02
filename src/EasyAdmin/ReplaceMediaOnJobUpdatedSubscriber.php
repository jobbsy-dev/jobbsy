<?php

namespace App\EasyAdmin;

use App\Entity\Job;
use App\Media\MediaFactory;
use App\Media\MediaRemover;
use App\Media\MediaUploader;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: BeforeEntityUpdatedEvent::class)]
final readonly class ReplaceMediaOnJobUpdatedSubscriber
{
    public function __construct(
        private MediaFactory $mediaFactory,
        private MediaUploader $mediaUploader,
        private MediaRemover $mediaRemover,
    ) {
    }

    /**
     * @param BeforeEntityUpdatedEvent<Job> $beforeEntityUpdatedEvent
     */
    public function __invoke(BeforeEntityUpdatedEvent $beforeEntityUpdatedEvent): void
    {
        $entity = $beforeEntityUpdatedEvent->getEntityInstance();

        if (!$entity instanceof Job) {
            return;
        }

        if (null === ($media = $entity->getOrganizationImage())) {
            return;
        }

        if (null === ($file = $media->getFile())) {
            return;
        }

        $this->mediaRemover->delete($media);

        $newMedia = $this->mediaFactory->createFromUploadedFile($file);
        $entity->changeOrganizationImage($newMedia);
        $this->mediaUploader->upload($newMedia);
    }
}
