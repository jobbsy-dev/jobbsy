<?php

namespace App\Controller;

use App\Donation\Command\CreateDonationPaymentUrlCommand;
use App\Donation\CommandHandler\CreateDonationPaymentUrlCommandHandler;
use App\Entity\Job;
use App\Form\PostJobOfferType;
use App\Form\SponsorType;
use App\Form\SubscriptionType;
use App\Job\Command\PostJobOfferCommand;
use App\Job\Command\PostJobOfferCommandHandler;
use App\Job\EmploymentType;
use App\Job\LocationType;
use App\Repository\JobRepository;
use App\Subscription\SubscribeMailingListCommand;
use App\Subscription\SubscribeMailingListCommandHandler;
use Doctrine\ORM\EntityManagerInterface;
use League\Uri\Modifier;
use Psr\Log\LoggerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class JobController extends AbstractController
{
    public function __construct(
        #[Autowire('%env(STRIPE_API_KEY)%')]
        private readonly string $stripeApiKey,
        private readonly CreateDonationPaymentUrlCommandHandler $commandHandler,
        private readonly JobRepository $jobRepository,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
        private readonly PostJobOfferCommandHandler $postJobOfferCommandHandler,
    ) {
    }

    #[Route('/', name: 'job_index', defaults: ['_format' => 'html'], methods: ['GET']), ]
    public function index(): Response
    {
        return $this->render('job/index.html.twig', [
            'featuredJobs' => $this->jobRepository->findFeaturedJobs(),
            'jobs' => $this->jobRepository->findLastJobs(),
            'locationTypes' => LocationType::cases(),
            'employmentTypes' => EmploymentType::cases(),
        ]);
    }

    #[Route('/rss.xml', name: 'job_rss', defaults: ['_format' => 'xml'], methods: ['GET']), ]
    public function rss(): Response
    {
        return $this->render('job/index.xml.twig', [
            'jobs' => $this->jobRepository->findBy([], ['createdAt' => 'DESC'], 10),
        ]);
    }

    #[Route('/job/new', name: 'job_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $command = new PostJobOfferCommand();
        $form = $this->createForm(PostJobOfferType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job = $this->postJobOfferCommandHandler->__invoke($command);

            $this->addFlash('success', 'Job posted successfully!');

            if (null === $command->donationAmount) {
                return $this->redirectToRoute('job_index');
            }

            $successUrl = $this->generateUrl('job_donation_success', [
                'id' => $job->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $successUrl .= '?session_id={CHECKOUT_SESSION_ID}'; // Stripe requires this parameter exactly like this (not encoded)

            $command = new CreateDonationPaymentUrlCommand(
                $job->getId(),
                $command->donationAmount,
                $successUrl,
                $this->generateUrl('job_donation_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL)
            );

            $redirectUrl = ($this->commandHandler)($command);

            return $this->redirect($redirectUrl, Response::HTTP_SEE_OTHER);
        }

        return $this->render('job/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/job/{id}/donation/success', name: 'job_donation_success', methods: ['GET'])]
    public function jobDonationSuccess(Job $job, Request $request): Response
    {
        $stripeSessionId = $request->query->getAlnum('session_id');

        Stripe::setApiKey($this->stripeApiKey);
        try {
            $session = Session::retrieve($stripeSessionId);
        } catch (\Exception $e) {
            $this->logger->notice($e->getMessage());

            throw $this->createNotFoundException();
        }

        if (Session::PAYMENT_STATUS_PAID === $session->payment_status) {
            $job->pinUntil(new \DateTimeImmutable('+6 months'));
            $this->em->flush();
        }

        return $this->render('job/donation_success.html.twig');
    }

    #[Route('/job/donation/cancel', name: 'job_donation_cancel', methods: ['GET'])]
    public function jobDonationCancel(): Response
    {
        return $this->render('job/donation_cancel.html.twig');
    }

    #[Route('/job/{id}', name: 'job', methods: ['GET'])]
    public function job(Job $job): RedirectResponse
    {
        $job->clicked();
        $this->em->flush();

        $uri = Modifier::from($job->getUrl())->appendQuery('ref=jobbsy');

        return $this->redirect($uri);
    }

    public function subscriptionForm(): Response
    {
        $form = $this->createForm(SubscriptionType::class);

        return $this->render('job/_subscription_form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/subscribe', name: 'subscribe', methods: ['POST'])]
    public function subscribe(
        Request $request,
        SubscribeMailingListCommandHandler $handler,
        #[Autowire('%env(MAILJET_CONTACT_LIST_ID)%')]
        string $mailjetListId
    ): Response {
        $form = $this->createForm(SubscriptionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new SubscribeMailingListCommand(
                $form->getData()['email'],
                $mailjetListId,
            );
            ($handler)($command);

            $this->addFlash('success', 'Subscribed!');

            return $this->redirectToRoute('job_index');
        }

        $this->addFlash('error', 'Please provide a valid email address');

        return $this->redirectToRoute('job_index');
    }

    #[Route('/job/{id}/sponsor', name: 'sponsor')]
    public function sponsor(Job $job, Request $request): Response
    {
        $job->pinUntil($job->getCreatedAt()->modify('+1 month'));
        $form = $this->createForm(SponsorType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $donationAmount = $form->get('donationAmount')->getData();

            $successUrl = $this->generateUrl('job_donation_success', [
                'id' => $job->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $successUrl .= '?session_id={CHECKOUT_SESSION_ID}'; // Stripe requires this parameter exactly like this (not encoded)

            $command = new CreateDonationPaymentUrlCommand(
                $job->getId(),
                $donationAmount,
                $successUrl,
                $this->generateUrl('job_donation_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL)
            );

            $redirectUrl = ($this->commandHandler)($command);

            return $this->redirect($redirectUrl, Response::HTTP_SEE_OTHER);
        }

        return $this->render('job/sponsor.html.twig', [
            'job' => $job,
            'form' => $form,
        ]);
    }

    #[Route('/symfony-location-{locationType}-jobs', name: 'job_location_type', methods: ['GET']), ]
    public function jobsByLocationType(LocationType $locationType): Response
    {
        return $this->render('job/location_type.html.twig', [
            'jobs' => $this->jobRepository->jobsByLocationType($locationType),
            'locationType' => $locationType,
        ]);
    }

    #[Route('/symfony-employment-{employmentType}-jobs', name: 'job_employment_type', methods: ['GET']), ]
    public function jobsByEmploymentType(EmploymentType $employmentType): Response
    {
        return $this->render('job/employment_type.html.twig', [
            'jobs' => $this->jobRepository->jobsByEmploymentType($employmentType),
            'employmentType' => $employmentType,
        ]);
    }
}
