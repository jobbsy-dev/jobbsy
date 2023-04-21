<?php

namespace App\Media;

use App\Entity\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\ByteString;

final class MediaFactory
{
    public function createFromUploadedFile(UploadedFile $file, string $pathPrefix = 'images'): Media
    {
        $media = new Media();

        $filename = sprintf('%s.%s', ByteString::fromRandom(36), $file->guessExtension());
        $path = sprintf('%s/%s', $pathPrefix, $filename);

        $media->setName($filename);
        $media->setOriginalName($file->getClientOriginalName());
        $imageSize = getimagesize($file);
        $dimensions = $imageSize ? array_splice($imageSize, 0, 2) : null;
        $media->setDimensions($dimensions);
        $media->setSize($file->getSize());
        $media->setMimeType($file->getMimeType());
        $media->setPath($path);

        $media->setFile($file);

        return $media;
    }
}
