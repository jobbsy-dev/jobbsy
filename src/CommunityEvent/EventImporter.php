<?php

namespace App\CommunityEvent;

use App\CommunityEvent\Repository\SourceRepositoryInterface;
use App\Entity\CommunityEvent\Event;
use Psr\Log\LoggerInterface;
use Symfony\Component\Intl\Countries;

final readonly class EventImporter
{
    public function __construct(
        private SourceRepositoryInterface $sourceRepository,
        private LoggerInterface $logger,
        private EventScraping $eventScraping
    ) {
    }

    public function import(): array
    {
        $events = [];
        $sources = $this->sourceRepository->getAll();

        foreach ($sources as $source) {
            try {
                $eventsData = $this->eventScraping->fetch($source->getUrl());

                foreach ($eventsData as $eventData) {
                    $event = new Event();
                    $event->setName(html_entity_decode($eventData['name']));
                    $event->setUrl($eventData['url']);
                    $event->setAbstract(sprintf(
                        '%s...',
                        mb_substr(html_entity_decode($eventData['description']), 0, 200))
                    );

                    switch (true) {
                        case str_contains((string) $eventData['eventAttendanceMode'], 'OnlineEventAttendanceMode'):
                            $event->setAttendanceMode(AttendanceMode::ONLINE);
                            break;
                        case str_contains((string) $eventData['eventAttendanceMode'], 'MixedEventAttendanceMode'):
                            $event->setAttendanceMode(AttendanceMode::MIXED);
                            break;
                        case str_contains((string) $eventData['eventAttendanceMode'], 'OfflineEventAttendanceMode'):
                            $event->setAttendanceMode(AttendanceMode::OFFLINE);
                            break;
                    }

                    $event->setLocation(html_entity_decode($eventData['location']['address']['addressLocality']));
                    $event->setStartDate(new \DateTimeImmutable($eventData['startDate']));
                    $event->setEndDate(new \DateTimeImmutable($eventData['endDate']));

                    $countryName = html_entity_decode($eventData['location']['address']['addressCountry']);
                    $key = array_search($countryName, Countries::getNames(), true);
                    $event->setCountry($key);

                    $events[] = $event;
                }
            } catch (\Throwable $throwable) {
                $this->logger->error($throwable->getMessage());
            }
        }

        return $events;
    }
}
