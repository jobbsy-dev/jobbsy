<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\EmploymentType;
use App\Repository\JobRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[Vich\Uploadable]
#[ApiResource(
    collectionOperations: ['get'],
    iri: 'https://schema.org/JobPosting',
    itemOperations: ['get'],
    normalizationContext: ['groups' => ['read']],
    order: ['createdAt' => 'DESC']
)]
#[ApiFilter(OrderFilter::class, properties: ['createdAt' => 'DESC'])]
class Job
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['read'])]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    private ?string $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    private ?string $location;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['read'])]
    #[ApiFilter(DateFilter::class)]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'string', enumType: EmploymentType::class)]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    private ?EmploymentType $employmentType;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    private ?string $organization;

    #[ORM\Column(type: 'json')]
    #[Assert\Count(max: 5)]
    #[Groups(['read'])]
    private array $tags = [];

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    private ?string $url;

    #[Vich\UploadableField(mapping: 'organization_image', fileNameProperty: 'organizationImageName', size: 'organizationImageSize')]
    private ?File $organizationImageFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['read'])]
    private ?string $organizationImageName = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $organizationImageSize = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['read'])]
    private ?string $organizationImageUrl = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $clickCount = 0;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['read'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    private ?string $source = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $pinnedUntil = null;

    public function __construct(?Uuid $id = null)
    {
        if (null === $id) {
            $id = Uuid::v4();
        }

        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getEmploymentType(): ?EmploymentType
    {
        return $this->employmentType;
    }

    public function setEmploymentType(EmploymentType $employmentType): void
    {
        $this->employmentType = $employmentType;
    }

    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    public function setOrganization(string $organization): void
    {
        $this->organization = $organization;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function setOrganizationImageFile(?File $organizationImageFile = null): void
    {
        $this->organizationImageFile = $organizationImageFile;

        if (null !== $organizationImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getOrganizationImageFile(): ?File
    {
        return $this->organizationImageFile;
    }

    public function setOrganizationImageName(?string $organizationImageName): void
    {
        $this->organizationImageName = $organizationImageName;
    }

    public function getOrganizationImageName(): ?string
    {
        return $this->organizationImageName;
    }

    public function setOrganizationImageSize(?int $organizationImageSize): void
    {
        $this->organizationImageSize = $organizationImageSize;
    }

    public function getOrganizationImageSize(): ?int
    {
        return $this->organizationImageSize;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getOrganizationImageUrl(): ?string
    {
        return $this->organizationImageUrl;
    }

    public function setOrganizationImageUrl(?string $organizationImageUrl): void
    {
        $this->organizationImageUrl = $organizationImageUrl;
    }

    public function clicked(): void
    {
        ++$this->clickCount;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): void
    {
        $this->source = $source;
    }

    public function pinUntil(\DateTimeImmutable $until): void
    {
        $this->pinnedUntil = $until;
    }

    public function isPinned(): bool
    {
        return $this->pinnedUntil > new \DateTimeImmutable();
    }
}
