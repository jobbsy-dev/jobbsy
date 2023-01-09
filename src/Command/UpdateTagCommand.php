<?php

namespace App\Command;

use App\Entity\Job;
use App\Entity\Tags;
use App\Repository\JobRepository;
use App\Repository\TagsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\RouterInterface;

#[AsCommand(
    name: 'app:update:CreateRelationJobTag',
    description: 'Create new Tag and relation between Job and Tag',
)]
class UpdateTagCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TagsRepository $tagsRepository,
        private readonly JobRepository $jobRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $oJobs = $this->jobRepository->findAll();

        $cptNewRelation = 0;
        $cptNewTag = 0;
        $cptJobRelationUpdated = 0;

        $progressBar = new ProgressBar($output, \count($oJobs));
        $progressBar->start();

        foreach ($oJobs as $eJob) {
            $jsonTags = $eJob->getTags();

            foreach ($jsonTags as $labelTag) {
                $labelTag = trim(trim($labelTag), "\"\'");
                $eTags = null;
                $res = $this->tagsRepository->findOneByLabelCI($labelTag);

                if (null === $res) {
                    $eTags = new Tags();
                    $eTags->setLabel($labelTag);
                    $this->em->persist($eTags);
                    $this->em->flush();
                    ++$cptNewTag;
                } else {
                    $eTags = $res;
                }

                $eJob->addTag($eTags);
                ++$cptNewRelation;
            }
            ++$cptJobRelationUpdated;
            $this->em->persist($eJob);
            $progressBar->advance();
        }

        $this->em->flush();
        $progressBar->finish();

        $io->info('There are '.$cptNewTag.' Tags created');
        $io->info('There are '.$cptNewRelation.' relations created');
        $io->info('There are '.$cptJobRelationUpdated.' job updated');

        $io->success('Successful update');

        return Command::SUCCESS;
    }
}
