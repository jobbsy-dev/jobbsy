<?php

namespace App\ConsoleCommand;

use App\Repository\JobRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Scheduler\Attribute\AsPeriodicTask;

#[AsCommand(
    name: 'app:clear-pinned',
    description: 'Clear pinned jobs',
)]
#[AsPeriodicTask(frequency: '1 day')]
final class ClearPinnedCommand extends Command
{
    public function __construct(private readonly JobRepository $jobRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->jobRepository->clearExpiredPinnedJobs();

        return Command::SUCCESS;
    }
}
