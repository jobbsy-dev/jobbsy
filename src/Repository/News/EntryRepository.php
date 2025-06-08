<?php

namespace App\Repository\News;

use App\Entity\News\Entry;
use App\News\EntryRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Entry>
 *
 * @method Entry|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entry|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entry[]    findAll()
 * @method Entry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EntryRepository extends ServiceEntityRepository implements EntryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entry::class);
    }

    public function save(Entry $entry): void
    {
        $this->getEntityManager()->persist($entry);
    }

    public function remove(Entry $entry): void
    {
        $this->getEntityManager()->remove($entry);
    }

    public function createQueryBuilderLastNews(): QueryBuilder
    {
        return $this->createQueryBuilder('entry')
            ->orderBy('entry.publishedAt', Criteria::DESC);
    }

    public function ofLink(string $link): ?Entry
    {
        return $this->findOneBy(['link' => $link]);
    }

    public function getAll(): array
    {
        return $this->findAll();
    }
}
