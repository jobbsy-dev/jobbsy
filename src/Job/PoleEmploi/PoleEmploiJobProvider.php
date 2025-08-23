<?php

namespace App\Job\PoleEmploi;

use App\Entity\Job;
use App\Job\EmploymentType;
use App\Job\JobCollection;
use App\Job\JobProviderInterface;
use App\Job\SearchParameters;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class PoleEmploiJobProvider implements JobProviderInterface
{
    public function __construct(
        private PoleEmploiApi $api,
        #[Autowire(env: 'POLE_EMPLOI_CLIENT_ID')]
        private string $poleEmploiClientId,
        private LoggerInterface $logger,
    ) {
    }

    public function retrieve(SearchParameters $parameters): JobCollection
    {
        $jobs = new JobCollection();

        try {
            $this->api->authenticate([
                'api_offresdemploiv2',
                'o2dsoffre',
                'application_'.$this->poleEmploiClientId,
            ]);
        } catch (\Throwable $throwable) {
            $this->logger->error('Error while authenticating pole emploi', [
                'exception' => $throwable->getMessage(),
            ]);

            return $jobs;
        }

        try {
            $results = $this->api->search([
                'motsCles' => 'symfony',
                'minCreationDate' => $parameters->from,
                'maxCreationDate' => $parameters->to,
                'origineOffre' => 1,
            ]);
        } catch (\Throwable $throwable) {
            $this->logger->error('Error while searching pole emploi', [
                'exception' => $throwable->getMessage(),
            ]);

            return $jobs;
        }

        foreach ($results as $result) {
            if (false === $this->isOfferCheckConditions($result)) {
                continue;
            }

            $location = $result['lieuTravail']['libelle'];
            if (str_contains((string) $location, '-')) {
                $location = ucfirst(mb_trim(explode('-', (string) $location)[1]));
            }

            $employmentType = EmploymentType::FULLTIME;
            if (isset($result['typeContrat'], $result['natureContrat'])
                && 'CDD' === $result['typeContrat']
                && str_contains((string) $result['natureContrat'], 'apprentissage')
            ) {
                $employmentType = EmploymentType::INTERNSHIP;
            }

            $job = new Job(
                title: $result['intitule'],
                location: $location,
                employmentType: $employmentType,
                organization: $result['entreprise']['nom'],
                url: $result['origineOffre']['urlOrigine']
            );

            $job->setIndustry($result['secteurActiviteLibelle'] ?? null);
            $job->setDescription($result['description'] ?? null);

            $job->setOrganizationImageUrl($result['entreprise']['logo'] ?? null);

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

        if (false === isset($jobOffer['entreprise']['logo'])) {
            return false;
        }

        $wordsIntitule = explode(' ', (string) $jobOffer['intitule']);
        if (\in_array('Front', $wordsIntitule, true) && \in_array('Angular', $wordsIntitule, true)) {
            return false;
        }

        return false !== isset($jobOffer['origineOffre']['urlOrigine']);
    }

    public function enabled(): bool
    {
        return true;
    }
}
