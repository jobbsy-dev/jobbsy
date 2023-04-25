<?php

namespace App\Repository;

use App\Entity\Job;
use App\Job\EmploymentType;
use App\Job\LocationType;
use App\Job\Repository\JobRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Job>
 */
final class JobRepository extends ServiceEntityRepository implements JobRepositoryInterface
{
    private const MAX_JOBS_PER_PAGE = 30;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    /**
     * @return Job[]
     */
    public function findLastJobs(int $limit = self::MAX_JOBS_PER_PAGE): array
    {
        $qb = $this->createQueryBuilder('job');

        return $qb
            ->where($qb->expr()->isNotNull('job.publishedAt'))
            ->addOrderBy('job.publishedAt', Criteria::DESC)
            ->addOrderBy('job.pinnedUntil', Criteria::DESC)
            ->setMaxResults($limit)
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
            throw new JobNotFoundException(sprintf('Job with id "%s" not found.', $id));
        }

        return $job;
    }

    public function save(Job $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Job $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Job[]
     */
    public function jobsByLocationType(LocationType $locationType, int $limit = self::MAX_JOBS_PER_PAGE): array
    {
        $qb = $this->createQueryBuilder('job');

        return $qb
            ->where($qb->expr()->isNotNull('job.publishedAt'))
            ->andWhere('job.locationType = :locationType')
            ->setParameter('locationType', $locationType)
            ->addOrderBy('job.pinnedUntil', Criteria::DESC)
            ->addOrderBy('job.publishedAt', Criteria::DESC)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Job[]
     */
    public function jobsByEmploymentType(EmploymentType $employmentType, int $limit = self::MAX_JOBS_PER_PAGE): array
    {
        $qb = $this->createQueryBuilder('job');

        return $qb
            ->where($qb->expr()->isNotNull('job.publishedAt'))
            ->andWhere('job.employmentType = :employmentType')
            ->setParameter('employmentType', $employmentType)
            ->addOrderBy('job.pinnedUntil', Criteria::DESC)
            ->addOrderBy('job.publishedAt', Criteria::DESC)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
