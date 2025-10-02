<?php

namespace App\EasyAdmin;

use App\Entity\News\Feed;
use App\Media\MediaFactory;
use App\Media\MediaUploader;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: BeforeEntityPersistedEvent::class)]
final readonly class UploadMediaOnFeedCreatedSubscriber
{
    public function __construct(private MediaFactory $mediaFactory, private MediaUploader $mediaUploader)
    {
    }

    /**
     * @param BeforeEntityPersistedEvent<Feed> $beforeEntityPersistedEvent
     */
    public function __invoke(BeforeEntityPersistedEvent $beforeEntityPersistedEvent): void
    {
        $entity = $beforeEntityPersistedEvent->getEntityInstance();

        if (!$entity instanceof Feed) {
            return;
        }

        if (null === $entity->getImageFile()) {
            return;
        }

        $media = $this->mediaFactory->createFromUploadedFile($entity->getImageFile());
        $entity->changeImage($media);
        $this->mediaUploader->upload($media);
    }
}
