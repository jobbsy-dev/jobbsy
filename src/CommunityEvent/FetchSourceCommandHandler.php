<?php

declare(strict_types=1);

namespace App\CommunityEvent;

use App\Entity\CommunityEvent\Event;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class FetchSourceCommandHandler
{
    public function __construct(
        private SourceRepositoryInterface $sourceRepository,
        private LoggerInterface $logger,
        private EventScraping $eventScraping,
        private EventRepositoryInterface $eventRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(FetchSourceCommand $command): void
    {
        $source = $this->sourceRepository->get($command->sourceId);

        if (null === $source) {
            return;
        }

        if (null === $source->getUrl()) {
            return;
        }

        $events = [];
        try {
            $eventsData = $this->eventScraping->fetch($source->getUrl());

            foreach ($eventsData as $eventData) {
                $event = new Event();
                $name = html_entity_decode((string) $eventData['name']);

                if (mb_strlen($name) > 255) {
                    continue;
                }

                $event->setName($name);
                $event->setUrl($eventData['url']);
                if (isset($eventData['description'])) {
                    $event->setAbstract(\sprintf(
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

                        $countryName = strtoupper(html_entity_decode((string) $eventData['location']['address']['addressCountry']));
                        if (Countries::exists($countryName)) {
                            $event->setCountry($countryName);
                        } else {
                            $key = array_search($countryName, Countries::getNames(), true);
                            if (false !== $key) {
                                $event->setCountry($key);
                            }
                        }
                    } elseif ('VirtualLocation' === $eventData['location']['@type']) {
                        $event->setAttendanceMode(AttendanceMode::ONLINE);
                    }
                }

                $events[] = $event;
            }
        } catch (\Throwable $throwable) {
            $this->logger->notice(
                \sprintf('Unable to fetch events from source "%s". Reason: %s', $source->getUrl(), $throwable->getMessage()),
                [
                    'sourceId' => $source->getId(),
                ]
            );

            return;
        }

        foreach ($events as $event) {
            if ('' === $event->getUrl()) {
                continue;
            }

            if (null !== $this->eventRepository->ofUrl($event->getUrl())) {
                continue;
            }

            $this->eventRepository->save($event);
        }

        $this->entityManager->flush();
    }
}
