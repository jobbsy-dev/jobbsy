<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Embeddable]
final class Media
{
    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $path = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $originalName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $mimeType = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $size = null;

    /**
     * @var int[]|null
     */
    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    private ?array $dimensions = null;

    private ?UploadedFile $file = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): void
    {
        $this->originalName = $originalName;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): void
    {
        $this->size = $size;
    }

    /**
     * @return int[]|null
     */
    public function getDimensions(): ?array
    {
        return $this->dimensions;
    }

    /**
     * @param int[]|null $dimensions
     */
    public function setDimensions(?array $dimensions): void
    {
        $this->dimensions = $dimensions;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function setFile(?UploadedFile $file): void
    {
        $this->file = $file;
    }

    public function getContent(): ?string
    {
        return $this->file?->getContent();
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }
}
