<?php

namespace App\Command;

use App\Job\Event\JobPostedEvent;
use App\Job\JobProvider;
use App\Job\SearchParameters;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Zenstruck\ScheduleBundle\Schedule\SelfSchedulingCommand;
use Zenstruck\ScheduleBundle\Schedule\Task\CommandTask;

#[AsCommand(
    name: 'app:job-provider:retrieve',
    description: 'Retrieve jobs from different sources',
)]
final class JobProviderPullCommand extends Command implements SelfSchedulingCommand
{
    public function __construct(
        private readonly JobProvider $provider,
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly JobRepository $jobRepository,
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

        $progressBar = new ProgressBar($output, $jobs->count());
        $progressBar->start();
        $i = 0;
        $events = [];
        foreach ($jobs->all() as $job) {
            if (null !== $this->jobRepository->findOneBy(['url' => $job->getUrl()])) {
                continue;
            }

            $this->entityManager->persist($job);

            $events[] = new JobPostedEvent($job);

            if (0 === ($i % 20)) {
                $this->entityManager->flush();
                $this->entityManager->clear();

                $this->dispatchEvents($events);
                $events = [];
            }
            ++$i;
            $progressBar->advance();
        }

        $this->entityManager->flush();

        $this->dispatchEvents($events);

        $progressBar->finish();

        $io->success('Successful pull');

        return Command::SUCCESS;
    }

    /**
     * @param JobPostedEvent[] $events
     */
    private function dispatchEvents(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatcher->dispatch($event);
        }
    }

    public function schedule(CommandTask $task): void
    {
        $task->twiceDaily(3, 10);
    }
}
