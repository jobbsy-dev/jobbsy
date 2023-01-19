<?php

namespace App\Command;

use App\Entity\Job;
use App\Entity\Tag;
use App\Repository\JobRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputOption ;
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

    protected int $cptNewRelation;
    protected int $cptNewTag;
    protected int $cptJobRelationUpdated;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TagRepository          $tagsRepository,
        private readonly JobRepository          $jobRepository,
    )
    {
        parent::__construct();
        $this->cptNewTag = 0;
        $this->cptNewRelation = 0;
        $this->cptJobRelationUpdated = 0;
    }

    protected function configure()
    {
        $this
            ->setName('app:update:CreateRelationJobTag')
            ->setDescription('Create new Tag and relation between Job and Tag')
            ->addOption('batch', 'b', InputOption::VALUE_OPTIONAL, 'Number of jobs to process in a batch', null)
            ->addOption('first', 'f', InputOption::VALUE_OPTIONAL, 'First job to process', null)
            ->addOption('last', 'l', InputOption::VALUE_OPTIONAL, 'Last job to process', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $batch = $input->getOption('batch');
        $first = $input->getOption('first');
        $last = $input->getOption('last');

        dump($batch);

        if (!is_null($batch) && !is_numeric($batch)) {
            throw new \InvalidArgumentException("batch must be a number");
        }
        if (!is_null($first) && !is_numeric($first)) {
            throw new \InvalidArgumentException("first must be a number");
        }
        if (!is_null($last) && !is_numeric($last)) {
            throw new \InvalidArgumentException("last must be a number");
        }

        $io = new SymfonyStyle($input, $output);

        $nbrTotalJob = $this->jobRepository->countJob();


        if ($batch) {
            $progressBar = new ProgressBar($output, $nbrTotalJob);
            $progressBar->start();
            $nbrStart = 0;
            while ($oJobs = $this->jobRepository->findJobsByNbr($nbrStart, $batch)) {
                foreach ($oJobs as $eJob) {
                    $this->createRelationTagEntity($eJob);
                    $progressBar->advance();
                }
                $nbrStart = $nbrStart + $batch;
            }
            $this->em->flush();
        } else {
            $nbrTotalJob= abs($last - $first);
            $progressBar = new ProgressBar($output, $nbrTotalJob);
            $progressBar->start();
           $oJobs = $this->jobRepository->findJobsBetweenPublished($first, $nbrTotalJob);
            foreach ($oJobs as $eJob) {
                $this->createRelationTagEntity($eJob);
                $progressBar->advance();
            }

            $this->em->flush();
        }

        $progressBar->finish();

        $io->info('There are ' . $this->cptNewTag . ' Tag created');
        $io->info('There are ' . $this->cptNewRelation . ' relations created');
        $io->info('There are ' . $this->cptJobRelationUpdated . ' job updated');

        $io->success('Successful update');

        return Command::SUCCESS;
    }
    public function createRelationTagEntity(Job $eJob) : int
    {
        $jsonTags = $eJob->getTags();
        foreach ($jsonTags as $labelTag) {
            $labelTag = trim(trim($labelTag), "\"\'");
            $eTags = null;
            $res = $this->tagsRepository->findOneByLabelCI($labelTag);

            if (null === $res) {
                $eTags = new Tag();
                $eTags->setLabel($labelTag);
                $this->em->persist($eTags);
                $this->em->flush();
                ++ $this->cptNewTag;
            }else {
                $eTags = $res;
            }

            $eJob->addTag($eTags);
            ++$this->cptNewRelation;
        }
    ++$this->cptJobRelationUpdated;
    $this->em->persist($eJob);

    return true;
    }


}
