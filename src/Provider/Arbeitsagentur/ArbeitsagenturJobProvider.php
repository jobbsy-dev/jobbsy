<?php

namespace App\Provider\Arbeitsagentur;

use App\EmploymentType;
use App\Entity\Job;
use App\Provider\JobCollection;
use App\Provider\JobProviderInterface;
use App\Provider\SearchParameters;
use League\Flysystem\FilesystemOperator;

final class ArbeitsagenturJobProvider implements JobProviderInterface
{
    public function __construct(
        private readonly ArbeitsagenturApi $api,
        private readonly FilesystemOperator $organizationImageStorage,
    ) {
    }

    public function retrieve(SearchParameters $parameters): JobCollection
    {
        $this->api->authenticate();

        $params = [
            'was' => 'symfony',
        ];
        if (null !== $parameters->to && null !== $parameters->from) {
            $params['veroeffentlichtseit'] = $parameters->to->diff($parameters->from);
        }

        $data = $this->api->search($params);

        $jobs = new JobCollection();
        foreach ($data as $datum) {
            if (false === $this->supports($datum)) {
                continue;
            }

            $job = new Job();
            $job->setTitle($datum['titel']);
            $job->setLocation($datum['arbeitsort']['ort']);
            $job->setOrganization($datum['arbeitgeber']);
            $job->setEmploymentType(EmploymentType::FULL_TIME);

            $url = $datum['externeUrl'] ?? 'https://www.arbeitsagentur.de/jobsuche/suche?id='.$datum['refnr'];
            $job->setUrl($url);

            $this->fillLogo($datum, $job);

            $job->setSource('Arbeitsagentur');

            $jobs->addJob($job);
        }

        return $jobs;
    }

    private function supports(array $data): bool
    {
        if (false === isset($data['titel'])) {
            return false;
        }

        if (false === isset($data['arbeitsort']['ort'])) {
            return false;
        }

        if (false === isset($data['arbeitgeber'])) {
            return false;
        }

        if (false === isset($data['refnr']) && false === isset($data['externeUrl'])) {
            return false;
        }

        return true;
    }

    private function fillLogo(array $data, Job $job): void
    {
        if (false === isset($data['logoHashId'])) {
            return;
        }

        $organizationLogo = $this->api->getOrganizationLogo($data['logoHashId']);

        if (null === $organizationLogo) {
            return;
        }

        if (null === $organizationLogo->contentType) {
            return;
        }

        $name = sprintf(
            '%s.%s',
            $data['logoHashId'],
            explode('/', $organizationLogo->contentType)[1]
        );
        $this->organizationImageStorage->write($name, $organizationLogo->content);
        $job->setOrganizationImageName($name);
    }
}
