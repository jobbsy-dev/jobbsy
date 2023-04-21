<?php

namespace App\EasyAdmin;

use App\Entity\Job;
use App\Media\MediaFactory;
use App\Media\MediaRemover;
use App\Media\MediaUploader;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class ReplaceMediaOnJobUpdatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MediaFactory $mediaFactory,
        private MediaUploader $mediaUploader,
        private MediaRemover $mediaRemover,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityUpdatedEvent::class => 'replaceMedia',
        ];
    }

    public function replaceMedia(BeforeEntityUpdatedEvent $beforeEntityUpdatedEvent): void
    {
        $entity = $beforeEntityUpdatedEvent->getEntityInstance();

        if (!$entity instanceof Job) {
            return;
        }

        if (null === ($file = $entity->getOrganizationImage()?->getFile())) {
            return;
        }

        $media = $entity->getOrganizationImage();

        if (null !== $media) {
            $this->mediaRemover->delete($media);
        }

        $newMedia = $this->mediaFactory->createFromUploadedFile($file);
        $entity->changeOrganizationImage($newMedia);
        $this->mediaUploader->upload($newMedia);
    }
}
