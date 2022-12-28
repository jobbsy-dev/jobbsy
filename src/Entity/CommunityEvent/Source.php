<?php

namespace App\Entity\CommunityEvent;

use App\CommunityEvent\SourceType;
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
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(type: Types::STRING, enumType: SourceType::class)]
    private ?SourceType $type = null;

    public function __construct(?UuidInterface $id = null)
    {
        if (null === $id) {
            $id = Uuid::uuid4();
        }

        $this->id = $id;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getType(): ?SourceType
    {
        return $this->type;
    }

    public function setType(SourceType $type): self
    {
        $this->type = $type;

        return $this;
    }
}
