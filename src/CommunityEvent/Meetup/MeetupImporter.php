<?php

namespace App\CommunityEvent\Meetup;

use App\CommunityEvent\AttendanceMode;
use App\CommunityEvent\SourceType;
use App\Entity\CommunityEvent\Event;
use App\Repository\CommunityEvent\SourceRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Intl\Countries;

final readonly class MeetupImporter
{
    public function __construct(
        private MeetupCrawler $crawler,
        private SourceRepository $sourceRepository,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @return Event[]
     */
    public function import(): array
    {
        $events = [];

        $groups = $this->sourceRepository->findBy([
            'type' => SourceType::MEETUP_GROUP,
        ]);

        foreach ($groups as $group) {
            try {
                $meetupsData = $this->crawler->crawl(sprintf('%s/events', $group->getUrl()));

                foreach ($meetupsData as $meetupData) {
                    $event = new Event();
                    $event->setName(html_entity_decode($meetupData['name']));
                    $event->setUrl($meetupData['url']);
                    $event->setAbstract(sprintf(
                        '%s...',
                        mb_substr(html_entity_decode($meetupData['description']), 0, 200))
                    );

                    switch (true) {
                        case str_contains((string) $meetupData['eventAttendanceMode'], 'OnlineEventAttendanceMode'):
                            $event->setAttendanceMode(AttendanceMode::ONLINE);
                            break;
                        case str_contains((string) $meetupData['eventAttendanceMode'], 'MixedEventAttendanceMode'):
                            $event->setAttendanceMode(AttendanceMode::MIXED);
                            break;
                        case str_contains((string) $meetupData['eventAttendanceMode'], 'OfflineEventAttendanceMode'):
                            $event->setAttendanceMode(AttendanceMode::OFFLINE);
                            break;
                    }

                    $event->setLocation(html_entity_decode($meetupData['location']['address']['addressLocality']));
                    $event->setStartDate(new \DateTimeImmutable($meetupData['startDate']));
                    $event->setEndDate(new \DateTimeImmutable($meetupData['endDate']));

                    $countryName = html_entity_decode($meetupData['location']['address']['addressCountry']);
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
