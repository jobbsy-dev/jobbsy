<?php

namespace App\Tests\Repository;

use App\CommunityEvent\EventRepositoryInterface;
use App\Entity\CommunityEvent\Event;

final class InMemoryEventRepository implements EventRepositoryInterface
{
    /**
     * @var array<string, Event>
     */
    private array $events = [];

    /**
     * @param Event[] $events
     */
    public function __construct(array $events = [])
    {
        foreach ($events as $event) {
            $this->events[(string) $event->getId()] = $event;
        }
    }

    public function save(Event $event): void
    {
        $this->events[(string) $event->getId()] = $event;
    }

    public function getAll(): array
    {
        return $this->events;
    }

    public function ofUrl(string $url): ?Event
    {
        foreach ($this->events as $event) {
            if ($event->getUrl() === $url) {
                return $event;
            }
        }

        return null;
    }

    public function findPastEvents(): array
    {
        return []; // Todo
    }

    public function findUpcomingEvents(?int $limit = null): array
    {
        return []; // Todo
    }

    public function remove(Event $event): void
    {
        if (false === isset($this->events[(string) $event->getId()])) {
            return;
        }

        unset($this->events[(string) $event->getId()]);
    }
}
