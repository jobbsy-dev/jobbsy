<?php

namespace App\Provider\PoleEmploi;

use App\EmploymentType;
use App\Entity\Job;
use App\Provider\JobCollection;
use App\Provider\JobProviderInterface;
use App\Provider\SearchParameters;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class PoleEmploiJobProvider implements JobProviderInterface
{
    public function __construct(
        private readonly PoleEmploiApi $api,
        #[Autowire('%env(POLE_EMPLOI_CLIENT_ID)%')]
        private readonly string $poleEmploiClientId
    ) {
    }

    public function retrieve(SearchParameters $parameters): JobCollection
    {
        $this->api->authenticate([
            'api_offresdemploiv2',
            'o2dsoffre',
            'application_'.$this->poleEmploiClientId,
        ]);

        $results = $this->api->search([
            'motsCles' => 'symfony',
            'minCreationDate' => $parameters->from,
            'maxCreationDate' => $parameters->to,
            'origineOffre' => 1,
        ]);

        $jobs = new JobCollection();
        foreach ($results as $result) {
            if (false === $this->isOfferCheckConditions($result)) {
                continue;
            }

            $job = new Job();
            $job->setOrganization($result['entreprise']['nom']);
            $job->setUrl($result['origineOffre']['urlOrigine']);
            $job->setTitle($result['intitule']);

            $location = $result['lieuTravail']['libelle'];
            if (str_contains($location, '-')) {
                $job->setLocation(ucfirst(trim(explode('-', $location)[1])));
            } else {
                $job->setLocation($location);
            }

            $job->setOrganizationImageUrl($result['entreprise']['logo'] ?? null);
            $job->setEmploymentType(EmploymentType::FULL_TIME);
            if (isset($result['typeContrat'], $result['natureContrat'])
                && 'CDD' === $result['typeContrat']
                && str_contains($result['natureContrat'], 'apprentissage')
            ) {
                $job->setEmploymentType(EmploymentType::INTERNSHIP);
            }

            if (isset($result['salaire']['libelle'])) {
                $job->setSalary($result['salaire']['libelle']);
            }

            $job->setTags(['PHP', 'Symfony']);
            $job->setSource('Pole Emploi');
            $job->publish();

            $jobs->addJob($job);
        }

        return $jobs;
    }

    private function isOfferCheckConditions(array $jobOffer): bool
    {
        if (false === isset($jobOffer['entreprise']['nom'])) {
            return false;
        }

        if (false === isset($jobOffer['lieuTravail']['libelle'])) {
            return false;
        }

        if (false === isset($jobOffer['origineOffre']['urlOrigine'])) {
            return false;
        }

        return true;
    }

    public function enabled(): bool
    {
        return true;
    }
}
