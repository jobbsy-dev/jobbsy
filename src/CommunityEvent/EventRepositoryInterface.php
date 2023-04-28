<?php

declare(strict_types=1);

namespace App\CommunityEvent;

use App\Entity\CommunityEvent\Event;

interface EventRepositoryInterface
{
    public function save(Event $event): void;

    /**
     * @return Event[]
     */
    public function getAll(): array;

    public function ofUrl(string $url): ?Event;

    /**
     * @return Event[]
     */
    public function findPastEvents(): array;

    /**
     * @return Event[]
     */
    public function findUpcomingEvents(?int $limit = null): array;

    public function remove(Event $event): void;
}
