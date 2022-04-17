<?php

namespace App\Command;

use App\Provider\JobProvider;
use App\Provider\SearchParameters;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:job-provider:retrieve',
    description: 'Retrieve jobs from different sources',
)]
class JobProviderPullCommand extends Command
{
    public function __construct(
        private readonly JobProvider $provider,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $now = new \DateTimeImmutable();

        $parameters = new SearchParameters();
        $parameters->from = $now->modify('-1 day');
        $parameters->to = $now;

        $jobs = $this->provider->retrieve($parameters);

        $this->entityManager->getConnection()->getConfiguration()?->setSQLLogger();

        $progressBar = new ProgressBar($output, $jobs->count());
        $progressBar->start();
        $i = 0;
        foreach ($jobs->all() as $job) {
            $this->entityManager->persist($job);

            if (0 === ($i % 20)) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
            ++$i;
            $progressBar->advance();
        }

        $this->entityManager->flush();
        $progressBar->finish();

        $io->success('Successful pull');

        return Command::SUCCESS;
    }
}
