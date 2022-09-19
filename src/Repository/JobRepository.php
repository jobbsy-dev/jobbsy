<?php

namespace App\Repository;

use App\Entity\Job;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

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
            ->where($qb->expr()->isNotNull('job.publishedAt'))
            ->addOrderBy('job.pinnedUntil', Criteria::DESC)
            ->addOrderBy('job.publishedAt', Criteria::DESC)
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

    /**
     * @return Job[]
     */
    public function findLastWeekJobs(): array
    {
        $qb = $this->createQueryBuilder('job');

        return $qb
            ->where('job.publishedAt > :lastWeek')
            ->andWhere($qb->expr()->isNotNull('job.publishedAt'))
            ->setParameter('lastWeek', new \DateTimeImmutable('-1 week'))
            ->orderBy('job.publishedAt', Criteria::DESC)
            ->addOrderBy('job.clickCount', Criteria::ASC)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function get(UuidInterface $id): Job
    {
        $job = $this->find($id);

        if (null === $job) {
            throw new JobNotFoundException(sprintf('Job with id "%s" not found', $id));
        }

        return $job;
    }
}
