<?php

namespace App\Entity\CommunityEvent;

use App\Repository\CommunityEvent\SourceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: SourceRepository::class)]
class Source
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidInterface $id;

    #[ORM\Column(length: 255)]
    private string $url = '';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $createdAt;

    public function __construct(?UuidInterface $id = null, \DateTimeImmutable $createdAt = new \DateTimeImmutable())
    {
        if (null === $id) {
            $id = Uuid::uuid4();
        }

        $this->id = $id;
        $this->createdAt = $createdAt;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
