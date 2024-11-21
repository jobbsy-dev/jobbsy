<?php

namespace App\Command;

use App\News\FetchFeedCommand;
use App\Repository\News\FeedRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand(
    name: 'app:aggregate-news',
    description: 'Aggregate news from multiple sources',
)]
#[AsCronTask(expression: '14 */12 * * *')]
final class AggregateNewsCommand extends Command
{
    public function __construct(
        private readonly FeedRepository $feedRepository,
        private readonly MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $feeds = $this->feedRepository->findAll();

        foreach ($feeds as $feed) {
            $this->bus->dispatch(new FetchFeedCommand($feed->getId()->toString()));

            $io->info(\sprintf('Feed "%s" fetching scheduled...', $feed->getName()));
        }

        return Command::SUCCESS;
    }
}
