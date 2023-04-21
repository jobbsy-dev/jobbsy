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
        $this->mediaStorage->write($media->getPath(), $media->getContent());
    }
}
