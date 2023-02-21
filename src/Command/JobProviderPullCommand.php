<?php

namespace App\Command;

use App\Job\Event\JobPostedEvent;
use App\Provider\JobProvider;
use App\Provider\SearchParameters;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsCommand(
    name: 'app:job-provider:retrieve',
    description: 'Retrieve jobs from different sources',
)] final class JobProviderPullCommand extends Command
{
    public function __construct(
        private readonly JobProvider $provider,
        private readonly EntityManagerInterface $entityManager,
        private readonly RouterInterface $router,
        #[Autowire('%env(COMMAND_ROUTER_HOST)%')] private readonly string $commandRouterHost,
        #[Autowire('%env(COMMAND_ROUTER_SCHEME)%')] private readonly string $commandRouterScheme,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly JobRepository $jobRepository
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

        $context = $this->router->getContext();
        $context->setHost($this->commandRouterHost);
        $context->setScheme($this->commandRouterScheme);

        $progressBar = new ProgressBar($output, $jobs->count());
        $progressBar->start();
        $i = 0;
        $events = [];
        foreach ($jobs->all() as $job) {
            if (null !== $this->jobRepository->findOneBy(['url' => $job->getUrl()])) {
                continue;
            }

            $this->entityManager->persist($job);

            $events[] = new JobPostedEvent(
                $job,
                $this->router->generate('job', ['id' => $job->getId()], RouterInterface::ABSOLUTE_URL),
            );

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
}
