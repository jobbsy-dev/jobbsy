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
                    $event->setName(html_entity_decode((string) $eventData['name']));
                    $event->setUrl($eventData['url']);
                    if (isset($eventData['description'])) {
                        $event->setAbstract(sprintf(
                            '%s...',
                            mb_substr(html_entity_decode((string) $eventData['description']), 0, 200))
                        );
                    }

                    $event->setStartDate(new \DateTimeImmutable($eventData['startDate']));
                    $event->setEndDate(new \DateTimeImmutable($eventData['endDate']));

                    if (str_contains((string) $eventData['eventAttendanceMode'], 'OnlineEventAttendanceMode')) {
                        $event->setAttendanceMode(AttendanceMode::ONLINE);
                    }

                    if (str_contains((string) $eventData['eventAttendanceMode'], 'MixedEventAttendanceMode')) {
                        $event->setAttendanceMode(AttendanceMode::MIXED);
                    }

                    if (str_contains((string) $eventData['eventAttendanceMode'], 'OfflineEventAttendanceMode')) {
                        $event->setAttendanceMode(AttendanceMode::OFFLINE);
                    }

                    if (isset($eventData['location']['@type'])) {
                        if ('Place' === $eventData['location']['@type']) {
                            $event->setLocation(html_entity_decode((string) $eventData['location']['address']['addressLocality']));

                            $countryName = html_entity_decode((string) $eventData['location']['address']['addressCountry']);
                            if (Countries::exists($countryName)) {
                                $event->setCountry($countryName);
                            } else {
                                $key = array_search($countryName, Countries::getNames(), true);
                                $event->setCountry($key);
                            }
                        } elseif ('VirtualLocation' === $eventData['location']['@type']) {
                            $event->setAttendanceMode(AttendanceMode::ONLINE);
                        }
                    }

                    $events[] = $event;
                }
            } catch (\Throwable $throwable) {
                $this->logger->error($throwable->getMessage());
            }
        }

        return $events;
    }
}
