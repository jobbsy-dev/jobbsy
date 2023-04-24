<?php

namespace App\Media;

use App\Entity\Media;
use League\Flysystem\FilesystemOperator;

final readonly class MediaUploader
{
    public function __construct(private FilesystemOperator $mediaStorage)
    {
    }

    public function upload(Media $media): void
    {
        if (null === ($path = $media->getPath())) {
            return;
        }

        $this->mediaStorage->write($path, $media->getContent());
    }
}
