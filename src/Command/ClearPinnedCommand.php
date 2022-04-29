<?php

namespace App\Command;

use App\Repository\JobRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:clear-pinned',
    description: 'Clear pinned jobs',
)]
class ClearPinnedCommand extends Command
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
