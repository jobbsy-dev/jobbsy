<?php

namespace App\Entity\CommunityEvent;

use ApiPlatform\Doctrine\Common\Filter\OrderFilterInterface;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\CommunityEvent\AttendanceMode;
use App\Repository\CommunityEvent\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @final
 */
#[ApiResource(
    types: ['https://schema.org/Event'],
    order: ['createdAt' => OrderFilterInterface::DIRECTION_DESC]
)]
#[GetCollection]
#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidInterface $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ApiProperty(types: ['https://schema.org/name'])]
    private string $name = '';

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotBlank]
    #[ApiProperty(types: ['https://schema.org/startDate'])]
    private \DateTimeImmutable $startDate;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotBlank]
    #[ApiProperty(types: ['https://schema.org/endDate'])]
    private \DateTimeImmutable $endDate;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[ApiProperty(types: ['https://schema.org/location'])]
    private ?string $location = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Assert\Country]
    private ?string $country = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 200)]
    #[ApiProperty(types: ['https://schema.org/description'])]
    private ?string $abstract = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    #[ApiProperty(types: ['https://schema.org/url'])]
    private string $url = '';

    #[ORM\Column(type: Types::STRING, nullable: true, enumType: AttendanceMode::class)]
    #[Groups(['read'])]
    #[ApiProperty(types: ['https://schema.org/eventAttendanceMode'])]
    private ?AttendanceMode $attendanceMode = null;

    public function __construct(?UuidInterface $id = null)
    {
        if (null === $id) {
            $id = Uuid::uuid4();
        }

        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
        $this->startDate = new \DateTimeImmutable();
        $this->endDate = new \DateTimeImmutable();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getAbstract(): ?string
    {
        return $this->abstract;
    }

    public function setAbstract(?string $abstract): self
    {
        $this->abstract = $abstract;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getAttendanceMode(): ?AttendanceMode
    {
        return $this->attendanceMode;
    }

    public function setAttendanceMode(AttendanceMode $attendanceMode): void
    {
        $this->attendanceMode = $attendanceMode;
    }

    public function isOnline(): bool
    {
        if (null !== $this->attendanceMode) {
            return AttendanceMode::ONLINE === $this->attendanceMode;
        }

        if (null === $this->location) {
            return false;
        }

        return 'online' === mb_strtolower(mb_trim($this->location));
    }

    public function isMixed(): bool
    {
        if (null !== $this->attendanceMode) {
            return AttendanceMode::MIXED === $this->attendanceMode;
        }

        if (null === $this->location) {
            return false;
        }

        return str_contains(mb_strtolower(mb_trim($this->location)), 'online');
    }
}
