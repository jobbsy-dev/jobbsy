<?php

namespace App\Repository\CommunityEvent;

use App\Entity\CommunityEvent\Source;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Source>
 *
 * @method Source|null find($id, $lockMode = null, $lockVersion = null)
 * @method Source|null findOneBy(array $criteria, array $orderBy = null)
 * @method Source[]    findAll()
 * @method Source[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class SourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Source::class);
    }

    public function save(Source $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Source $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
