<?php

namespace App\EasyAdmin;

use App\Entity\News\Feed;
use App\Media\MediaFactory;
use App\Media\MediaRemover;
use App\Media\MediaUploader;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class ReplaceMediaOnFeedUpdatedSubscriber implements EventSubscriberInterface
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

        if (!$entity instanceof Feed) {
            return;
        }

        if (null === ($file = $entity->getImageFile())) {
            return;
        }

        $media = $entity->getImage();

        if (null !== $media && null !== $media->getPath()) {
            $this->mediaRemover->delete($media);
        }

        $newMedia = $this->mediaFactory->createFromUploadedFile($file);
        $entity->changeImage($newMedia);
        $this->mediaUploader->upload($newMedia);
    }
}
