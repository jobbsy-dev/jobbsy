<?php

namespace App\Command;

use App\CommunityEvent\EventImporter;
use App\News\Aggregator\AggregateNews;
use App\Repository\CommunityEvent\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zenstruck\ScheduleBundle\Schedule\SelfSchedulingCommand;
use Zenstruck\ScheduleBundle\Schedule\Task\CommandTask;

#[AsCommand(
    name: 'app:aggregate-events',
    description: 'Aggregate events from multiple sources',
)]
final class AggregateCommunityEventCommand extends Command implements SelfSchedulingCommand
{
    public function __construct(
        protected readonly AggregateNews $aggregateNews,
        private readonly EventImporter $importer,
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
            if (null !== $this->eventRepository->findOneBy(['url' => $event->getUrl()])) {
                continue;
            }

            $this->eventRepository->save($event);
        }

        $this->em->flush();

        $io->success('Successful pull');

        return Command::SUCCESS;
    }

    public function schedule(CommandTask $task): void
    {
        $task->dailyAt('02:30');
    }
}
