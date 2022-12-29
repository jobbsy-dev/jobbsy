<?php

namespace App\Entity\News;

use ApiPlatform\Doctrine\Common\Filter\OrderFilterInterface;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\News\EntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    types: ['https://schema.org/Article'],
    normalizationContext: ['groups' => ['entry:read']],
    order: ['createdAt' => OrderFilterInterface::DIRECTION_DESC]
)]
#[GetCollection]
#[ORM\Entity(repositoryClass: EntryRepository::class)]
class Entry
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[Groups(groups: ['entry:read'])]
    private UuidInterface $id;

    #[ORM\Column(length: 255)]
    #[ApiProperty(types: ['https://schema.org/name'])]
    #[Groups(groups: ['entry:read'])]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true)]
    #[ApiProperty(types: ['https://schema.org/url'])]
    #[Groups(groups: ['entry:read'])]
    private ?string $link = null;

    #[ORM\Column(type: Types::TEXT)]
    #[ApiProperty(types: ['https://schema.org/description'])]
    #[Groups(groups: ['entry:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(groups: ['entry:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[ApiProperty(types: ['https://schema.org/datePublished'])]
    #[Groups(groups: ['entry:read'])]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Feed $feed = null;

    public function __construct(?UuidInterface $id = null)
    {
        if (null === $id) {
            $id = Uuid::uuid4();
        }

        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getFeed(): ?Feed
    {
        return $this->feed;
    }

    public function setFeed(Feed $feed): void
    {
        $this->feed = $feed;
    }

    #[Groups(groups: ['entry:read'])]
    public function getSourceName(): ?string
    {
        return $this->feed?->getName();
    }

    #[Groups(groups: ['entry:read'])]
    public function getSourceUrl(): ?string
    {
        return $this->feed?->getUrl();
    }
}
