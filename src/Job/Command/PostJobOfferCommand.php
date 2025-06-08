<?php

namespace App\Job\Command;

use App\Entity\Job;
use App\Job\EmploymentType;
use App\Job\LocationType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

final class PostJobOfferCommand
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 20, max: 255)]
    public string $title = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    public string $location = '';

    #[Assert\NotBlank]
    public ?EmploymentType $employmentType = EmploymentType::FULLTIME;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $organization = '';

    /**
     * @var string[]
     */
    #[Assert\Count(max: 5)]
    public array $tags = [];

    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    #[Assert\Url(requireTld: true)]
    public string $url = '';

    #[Assert\File(
        maxSize: '2M',
        extensions: ['jpg', 'jpeg', 'png', 'webp']
    )]
    public ?UploadedFile $organizationImageFile = null;

    public ?string $salary = null;

    #[Assert\Email]
    #[Assert\Length(min: 5, max: 255)]
    public string $contactEmail = '';

    #[Assert\NotBlank(allowNull: true)]
    public ?LocationType $locationType = LocationType::REMOTE;

    #[Assert\GreaterThanOrEqual(0)]
    public ?int $donationAmount = null;

    public function toEntity(): Job
    {
        \Webmozart\Assert\Assert::notNull($this->employmentType);
        \Webmozart\Assert\Assert::notNull($this->locationType);

        $job = new Job(
            $this->title,
            $this->location,
            $this->employmentType,
            $this->organization,
            $this->url
        );

        $job->setLocationType($this->locationType);
        $job->setContactEmail($this->contactEmail);
        $job->setSalary($this->salary);
        $job->setTags($this->tags);

        return $job;
    }
}
