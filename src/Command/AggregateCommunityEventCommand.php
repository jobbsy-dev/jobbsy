<?php

namespace App\Command;

use App\CommunityEvent\FetchSourceCommand;
use App\Repository\CommunityEvent\SourceRepository as EventSourceRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Zenstruck\ScheduleBundle\Schedule\SelfSchedulingCommand;
use Zenstruck\ScheduleBundle\Schedule\Task\CommandTask;

#[AsCommand(
    name: 'app:aggregate-events',
    description: 'Aggregate events from multiple sources',
)]
final class AggregateCommunityEventCommand extends Command implements SelfSchedulingCommand
{
    public function __construct(
        private readonly EventSourceRepository $sourceRepository,
        private readonly MessageBusInterface $bus
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $sources = $this->sourceRepository->findAll();

        foreach ($sources as $source) {
            $this->bus->dispatch(new FetchSourceCommand($source->getId()));
            $io->info(sprintf('Source "%s" fetching scheduled...', $source->getUrl()));
        }

        return Command::SUCCESS;
    }

    public function schedule(CommandTask $task): void
    {
        $task->daily();
    }
}
