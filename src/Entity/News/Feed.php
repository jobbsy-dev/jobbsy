<?php

namespace App\Entity\News;

use App\Entity\Media;
use App\News\Aggregator\FeedType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Feed implements \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidInterface $id;

    #[ORM\Column(length: 255)]
    private string $url = '';

    #[ORM\Column(length: 255)]
    private string $name = '';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::STRING, enumType: FeedType::class)]
    private FeedType $type = FeedType::RSS;

    #[ORM\Embedded(class: Media::class, columnPrefix: 'image_')]
    private ?Media $image = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $imageUrl = null;

    #[Assert\File(
        maxSize: '2M',
        extensions: ['jpg', 'jpeg', 'png', 'webp']
    )]
    private ?UploadedFile $imageFile = null;

    public function __construct(?UuidInterface $id = null)
    {
        if (null === $id) {
            $id = Uuid::uuid4();
        }

        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getType(): FeedType
    {
        return $this->type;
    }

    public function setType(FeedType $type): void
    {
        $this->type = $type;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function getImage(): ?Media
    {
        return $this->image;
    }

    public function changeImage(Media $media): void
    {
        $this->image = $media;
    }

    public function getImageFile(): ?UploadedFile
    {
        return $this->imageFile;
    }

    public function setImageFile(?UploadedFile $imageFile): void
    {
        $this->imageFile = $imageFile;
    }
}
