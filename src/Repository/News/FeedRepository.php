<?php

namespace App\Repository\News;

use App\Entity\News\Feed;
use App\News\FeedRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Feed>
 *
 * @method Feed|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feed|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feed[]    findAll()
 * @method Feed[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class FeedRepository extends ServiceEntityRepository implements FeedRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feed::class);
    }

    public function save(Feed $feed): void
    {
        $this->getEntityManager()->persist($feed);
        $this->getEntityManager()->flush();
    }

    public function remove(Feed $feed): void
    {
        $this->getEntityManager()->remove($feed);
        $this->getEntityManager()->flush();
    }

    public function get(string $id): ?Feed
    {
        return $this->find($id);
    }
}
