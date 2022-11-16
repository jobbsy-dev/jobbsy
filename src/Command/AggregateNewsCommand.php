<?php

namespace App\Command;

use App\News\Aggregator\AggregateNews;
use App\Repository\News\EntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:aggregate-news',
    description: 'Aggregate news from multiple sources',
)]
class AggregateNewsCommand extends Command
{
    public function __construct(
        protected readonly AggregateNews $aggregateNews,
        private readonly EntryRepository $articleRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $articles = ($this->aggregateNews)();

        $progressBar = new ProgressBar($output, \count($articles));
        $progressBar->start();
        $i = 0;
        foreach ($articles as $article) {
            if ($this->articleRepository->findOneBy(['link' => $article->getLink()])) {
                continue;
            }

            $this->entityManager->persist($article);

            if (0 === ($i % 20)) {
                $this->entityManager->flush();
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
