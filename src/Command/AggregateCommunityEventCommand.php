<?php

namespace App\Command;

use App\CommunityEvent\Meetup\MeetupImporter;
use App\News\Aggregator\AggregateNews;
use App\Repository\CommunityEvent\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:aggregate-events',
    description: 'Aggregate events from multiple sources',
)]
class AggregateCommunityEventCommand extends Command
{
    public function __construct(
        protected readonly AggregateNews $aggregateNews,
        private readonly MeetupImporter $importer,
        private readonly EventRepository $eventRepository,
        private readonly EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $events = $this->importer->import();

        foreach ($events as $event) {
            if ($this->eventRepository->findOneBy(['url' => $event->getUrl()])) {
                continue;
            }

            $this->eventRepository->save($event);
        }

        $this->em->flush();

        $io->success('Successful pull');

        return Command::SUCCESS;
    }
}
