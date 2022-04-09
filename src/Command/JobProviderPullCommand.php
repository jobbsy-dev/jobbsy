<?php

namespace App\Command;

use App\Provider\PoleEmploi\PoleEmploiProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:job-provider:pull',
    description: 'Add a short description for your command',
)]
class JobProviderPullCommand extends Command
{
    public function __construct(private readonly PoleEmploiProvider $provider)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $now = new \DateTimeImmutable();

        $this->provider->pull([
            'motsCles' => 'symfony,développeur',
            'minCreationDate' => $now->modify('-1 day'),
            'maxCreationDate' => $now,
            'origineOffre' => 1,
        ]);

        $io->success('Successful pull');

        return Command::SUCCESS;
    }
}
