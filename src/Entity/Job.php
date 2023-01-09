<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Common\Filter\OrderFilterInterface;
use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Job\EmploymentType;
use App\Job\LocationType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
    types: ['https://schema.org/JobPosting'],
    operations: [
        new Get(),
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['read']],
    order: ['createdAt' => OrderFilterInterface::DIRECTION_DESC]
)]
#[ORM\Entity]
#[Vich\Uploadable]
#[ApiFilter(filterClass: OrderFilter::class, properties: ['createdAt' => OrderFilterInterface::DIRECTION_DESC])]
class Job
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[Groups(['read'])]
    private UuidInterface $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    #[ApiFilter(filterClass: SearchFilter::class, strategy: SearchFilterInterface::STRATEGY_PARTIAL)]
    private ?string $title;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    #[ApiFilter(filterClass: SearchFilter::class, strategy: SearchFilterInterface::STRATEGY_PARTIAL)]
    private ?string $location;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::STRING, enumType: EmploymentType::class)]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    #[ApiFilter(filterClass: SearchFilter::class, strategy: SearchFilterInterface::STRATEGY_PARTIAL)]
    private ?EmploymentType $employmentType;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    #[ApiFilter(filterClass: SearchFilter::class, strategy: SearchFilterInterface::STRATEGY_PARTIAL)]
    private ?string $organization;

    #[ORM\Column(type: Types::JSON)]
    #[Assert\Count(max: 5)]
    #[Groups(['read'])]
    private array $tags = [];

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    private ?string $url;

    #[Vich\UploadableField(
        mapping: 'organization_image',
        fileNameProperty: 'organizationImageName',
        size: 'organizationImageSize'
    )]
    private ?File $organizationImageFile = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['read'])]
    private ?string $organizationImageName = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $organizationImageSize = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['read'])]
    private ?string $organizationImageUrl = null;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $clickCount = 0;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['read'])]
    #[ApiFilter(filterClass: SearchFilter::class, strategy: SearchFilterInterface::STRATEGY_PARTIAL)]
    private ?string $source = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $pinnedUntil = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $tweetId = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['read'])]
    #[ApiFilter(filterClass: DateFilter::class)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['read'])]
    private ?string $salary = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Assert\Email]
    private ?string $contactEmail = null;

    #[ORM\Column(type: Types::STRING, nullable: true, enumType: LocationType::class)]
    #[Assert\NotBlank]
    #[Groups(['read'])]
    #[ApiFilter(filterClass: SearchFilter::class, strategy: SearchFilterInterface::STRATEGY_PARTIAL)]
    private ?LocationType $locationType = null;

    #[ORM\ManyToMany(targetEntity: Tags::class, mappedBy: 'job')]
    private Collection $tag;

    public function __construct(?UuidInterface $id = null)
    {
        if (null === $id) {
            $id = Uuid::uuid4();
        }

        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->tag = new ArrayCollection();
    }

    public function getId(): UuidInterface
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

    public function getUpdatedAt(): \DateTimeImmutable
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

    public function getPinnedUntil(): ?\DateTimeImmutable
    {
        return $this->pinnedUntil;
    }

    public function setPinnedUntil(?\DateTimeImmutable $pinnedUntil): void
    {
        $this->pinnedUntil = $pinnedUntil;
    }

    public function getTweetId(): ?string
    {
        return $this->tweetId;
    }

    public function setTweetId(?string $tweetId): void
    {
        $this->tweetId = $tweetId;
    }

    public function publish(?\DateTimeImmutable $publishedAt = null): void
    {
        if (null === $publishedAt) {
            $publishedAt = new \DateTimeImmutable();
        }

        $this->publishedAt = $publishedAt;
    }

    public function unpublish(): void
    {
        $this->publishedAt = null;
    }

    public function isPublished(): bool
    {
        return null !== $this->publishedAt;
    }

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function setSalary(?string $salary): void
    {
        $this->salary = $salary;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): void
    {
        $this->contactEmail = $contactEmail;
    }

    public function getLocationType(): ?LocationType
    {
        return $this->locationType;
    }

    public function setLocationType(?LocationType $locationType): void
    {
        $this->locationType = $locationType;
    }

    /**
     * @return Collection<int, Tags>
     */
    public function getTag(): Collection
    {
        return $this->tag;
    }

    public function addTag(Tags $tag): self
    {
        if (!$this->tag->contains($tag)) {
            $this->tag->add($tag);
            $tag->addJob($this);
        }

        return $this;
    }

    public function removeTag(Tags $tag): self
    {
        if ($this->tag->removeElement($tag)) {
            $tag->removeJob($this);
        }

        return $this;
    }
}
