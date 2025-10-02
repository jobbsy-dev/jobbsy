<?php

namespace App\EasyAdmin;

use App\Entity\News\Feed;
use App\Media\MediaFactory;
use App\Media\MediaRemover;
use App\Media\MediaUploader;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: BeforeEntityUpdatedEvent::class)]
final readonly class ReplaceMediaOnFeedUpdatedSubscriber
{
    public function __construct(
        private MediaFactory $mediaFactory,
        private MediaUploader $mediaUploader,
        private MediaRemover $mediaRemover,
    ) {
    }

    /**
     * @param BeforeEntityUpdatedEvent<Feed> $beforeEntityUpdatedEvent
     */
    public function __invoke(BeforeEntityUpdatedEvent $beforeEntityUpdatedEvent): void
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
