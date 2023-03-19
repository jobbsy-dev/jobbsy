<?php

namespace App\Job\Command;

use App\Entity\Job;
use App\Job\EmploymentType;
use App\Job\LocationType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
final class PostJobOfferCommand
{
    #[Assert\NotBlank]
    public ?string $title = null;

    #[Assert\NotBlank]
    public ?string $location = null;

    #[Assert\NotBlank]
    public ?EmploymentType $employmentType = null;

    #[Assert\NotBlank]
    public ?string $organization = null;

    #[Assert\Count(max: 5)]
    public array $tags = [];

    #[Assert\NotBlank]
    public ?string $url = null;

    #[Vich\UploadableField(
        mapping: 'organization_image',
        fileNameProperty: 'organizationImageName',
        size: 'organizationImageSize'
    )]
    public ?File $organizationImageFile = null;

    public ?string $organizationImageName = null;

    public ?int $organizationImageSize = null;

    public ?string $salary = null;

    #[Assert\Email]
    public ?string $contactEmail = null;

    #[Assert\NotBlank(allowNull: true)]
    public ?LocationType $locationType = null;

    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\NotBlank]
    public ?int $donationAmount = 5000;

    public function toEntity(): Job
    {
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
        $job->setOrganizationImageFile($this->organizationImageFile);

        return $job;
    }
}
