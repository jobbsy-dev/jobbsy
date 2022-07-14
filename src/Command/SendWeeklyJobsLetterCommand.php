<?php

namespace App\Command;

use App\Mailjet\MailjetApi;
use App\Mailjet\Model\CreateCampaignDraft\CreateCampaignDraftRequest;
use App\Mailjet\Model\CreateCampaignDraftContent\CreateCampaignDraftContentRequest;
use App\Mailjet\Model\SendCampaignDraft\SendCampaignDraftRequest;
use App\Repository\JobRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Environment;

#[AsCommand(
    name: 'app:send-jobsletter',
    description: 'Add a short description for your command',
)]
class SendWeeklyJobsLetterCommand extends Command
{
    public function __construct(
        private readonly Environment $twig,
        private readonly JobRepository $jobRepository,
        private readonly MailjetApi $mailjetApi,
        #[Autowire('%env(MAILJET_CONTACT_LIST_ID)%')]
        private readonly int $mailjetContactListId,
        #[Autowire('%env(MAILJET_SENDER_ID)%')]
        private readonly string $mailjetSenderId,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jobs = $this->jobRepository->findLastWeekJobs();

        if (empty($jobs)) {
            $output->writeln('No jobs found');

            return Command::SUCCESS;
        }

        $response = $this->mailjetApi->createCampaignDraft(new CreateCampaignDraftRequest(
            sprintf('[%s] Weekly jobs letter', (new \DateTime())->format('W')),
            $this->mailjetContactListId,
            'en_US',
            'hello@jobbsy.dev',
            'Quentin from Jobbsy',
            'Weekly Symfony jobs 🚀',
            $this->mailjetSenderId,
        ));

        if (null === $response) {
            return Command::FAILURE;
        }

        if (false === isset($response->data[0]['ID'])) {
            return Command::FAILURE;
        }

        $id = $response->data[0]['ID'];

        $html = $this->twig->render('email/weekly_jobsletter.html.twig', [
            'jobs' => $jobs,
        ]);
        $this->mailjetApi->createCampaignDraftContent(new CreateCampaignDraftContentRequest($id, $html));

        $this->mailjetApi->sendCampaignDraft(new SendCampaignDraftRequest($id));

        return Command::SUCCESS;
    }
}
