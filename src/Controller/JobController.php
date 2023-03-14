<?php

namespace App\Controller;

use App\Analytics\AnalyticsClient;
use App\Analytics\Plausible\EventRequest;
use App\Donation\Command\CreateDonationPaymentUrlCommand;
use App\Donation\CommandHandler\CreateDonationPaymentUrlCommandHandler;
use App\Entity\Job;
use App\Form\JobType;
use App\Form\SponsorType;
use App\Form\SubscriptionType;
use App\Job\Event\JobPostedEvent;
use App\Repository\JobRepository;
use App\Subscription\SubscribeMailingListCommand;
use App\Subscription\SubscribeMailingListCommandHandler;
use Doctrine\ORM\EntityManagerInterface;
use League\Uri\Uri;
use League\Uri\UriModifier;
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
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class JobController extends AbstractController
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        #[Autowire('%env(STRIPE_API_KEY)%')]
        private readonly string $stripeApiKey,
        private readonly CreateDonationPaymentUrlCommandHandler $commandHandler,
        private readonly JobRepository $jobRepository,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
        private readonly AnalyticsClient $client,
        ) {
    }

    #[Route('/', name: 'job_index', defaults: ['_format' => 'html'], methods: ['GET']), ]
    public function index(): Response
    {
        return $this->render('job/index.html.twig', [
            'jobs' => $this->jobRepository->findLastJobs(),
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
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $job->publish();
            // Allow 1 month of boost on manual creation
            $job->pinUntil(new \DateTimeImmutable('+1 month'));
            $this->jobRepository->save($job, true);

            $this->addFlash('success', 'Job posted successfully!');

            $this->eventDispatcher->dispatch(new JobPostedEvent(
                $job,
                $this->generateUrl('job', ['id' => $job->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            ));

            $donationAmount = $form->get('donationAmount')->getData();
            if (0 === (int) $donationAmount) {
                return $this->redirectToRoute('job_index');
            }

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

        return $this->render('job/new.html.twig', [
            'job' => $job,
            'form' => $form,
        ]);
    }

    #[Route('/job/{id}/donation/success', name: 'job_donation_success', methods: ['GET'])]
    public function jobDonationSuccess(Job $job, Request $request): Response
    {
        if (null === ($stripeSessionId = $request->get('session_id'))) {
            throw $this->createNotFoundException();
        }

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
    public function job(Request $request, Job $job): RedirectResponse
    {
        $job->clicked();
        $this->em->flush();

        $this->client->event(EventRequest::create([
            'User-Agent' => $request->headers->get('User-Agent'),
            'X-Forwarded-For' => implode(',', $request->getClientIps()),
            'domain' => 'jobbsy.dev',
            'name' => 'pageview',
            'url' => $request->getUri(),
        ]));

        $uri = Uri::createFromString($job->getUrl());
        $uri = UriModifier::appendQuery($uri, 'ref=jobbsy');

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
}
