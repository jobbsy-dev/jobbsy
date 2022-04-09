<?php

namespace App\Provider\PoleEmploi;

use App\EmploymentType;
use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;

class PoleEmploiProvider
{
    public function __construct(
        private readonly PoleEmploiApi $api,
        private readonly EntityManagerInterface $entityManager,
        private readonly string $clientId
    ) {
    }

    public function pull(array $params = []): void
    {
        $this->api->authenticate([
            'api_offresdemploiv2',
            'o2dsoffre',
            'application_'.$this->clientId
        ]);

        $results = $this->api->search($params);

        $this->entityManager->getConnection()->getConfiguration()?->setSQLLogger(null);

        $i = 0;
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

            $job->setTags(['Symfony', 'API PoleEmploi']);

            $this->entityManager->persist($job);

            if (0 === ($i % 20)) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
            $i++;
        }

        $this->entityManager->flush();
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
}
