<?php

namespace App\Repository\CommunityEvent;

use App\CommunityEvent\EventRepositoryInterface;
use App\Entity\CommunityEvent\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Clock\ClockInterface;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EventRepository extends ServiceEntityRepository implements EventRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, private readonly ClockInterface $clock)
    {
        parent::__construct($registry, Event::class);
    }

    public function save(Event $event): void
    {
        $this->getEntityManager()->persist($event);
    }

    public function remove(Event $event): void
    {
        $this->getEntityManager()->remove($event);
    }

    /**
     * @return Event[]
     */
    public function findUpcomingEvents(int $limit = null): array
    {
        return $this->createQueryBuilder('event')
            ->where('event.startDate >= :today')
            ->setParameter('today', $this->clock->now())
            ->orderBy('event.startDate', Criteria::ASC)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[]
     */
    public function findPastEvents(): array
    {
        return $this->createQueryBuilder('event')
            ->where('event.startDate < :today')
            ->setParameter('today', $this->clock->now())
            ->orderBy('event.startDate', Criteria::DESC)
            ->getQuery()
            ->getResult();
    }

    public function getAll(): array
    {
        return $this->findAll();
    }

    public function ofUrl(string $url): ?Event
    {
        return $this->findOneBy(['url' => $url]);
    }
}
