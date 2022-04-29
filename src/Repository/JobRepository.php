<?php

namespace App\Repository;

use App\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    /**
     * @return Job[]
     */
    public function findLastJobs(): array
    {
        $qb = $this->createQueryBuilder('job');

        return $qb
            ->orderBy('job.pinnedUntil', Criteria::ASC)
            ->addOrderBy('job.createdAt', Criteria::DESC)
            ->setMaxResults(30)
            ->getQuery()
            ->getResult();
    }

    public function clearExpiredPinnedJobs(): void
    {
        $this->createQueryBuilder('job')
            ->update()
            ->set('job.pinnedUntil', ':pinnedUntil')
            ->setParameter('pinnedUntil', null)
            ->where('job.pinnedUntil < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();
    }
}
