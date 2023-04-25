<?php

namespace App\EasyAdmin;

use App\Entity\News\Feed;
use App\Media\MediaFactory;
use App\Media\MediaUploader;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class UploadMediaOnFeedCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(private MediaFactory $mediaFactory, private MediaUploader $mediaUploader)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'uploadMedia',
        ];
    }

    public function uploadMedia(BeforeEntityPersistedEvent $beforeEntityPersistedEvent): void
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
