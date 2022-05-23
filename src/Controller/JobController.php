<?php

namespace App\Controller;

use App\Entity\Job;
use App\Event\JobPostedEvent;
use App\Form\JobType;
use App\Form\SubscriptionType;
use App\Repository\JobRepository;
use App\Subscription\SubscribeMailingListCommand;
use App\Subscription\SubscribeMailingListCommandHandler;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class JobController extends AbstractController
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly string $stripeApiKey,
        private readonly string $taxRateId
    ) {
    }

    #[Route('/', name: 'job_index', defaults: ['_format' => 'html'], methods: ['GET']), ]
    public function index(JobRepository $jobRepository): Response
    {
        return $this->render('job/index.html.twig', [
            'jobs' => $jobRepository->findLastJobs(),
        ]);
    }

    #[Route('/rss.xml', name: 'job_rss', defaults: ['_format' => 'xml'], methods: ['GET']), ]
    public function rss(JobRepository $jobRepository): Response
    {
        return $this->render('job/index.xml.twig', [
            'jobs' => $jobRepository->findBy([], ['createdAt' => 'DESC'], 10),
        ]);
    }

    #[Route('/job/new', name: 'job_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($job);
            $entityManager->flush();

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

            Stripe::setApiKey($this->stripeApiKey);
            $session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Sponsor job offer & open source',
                        ],
                        'unit_amount' => $form->get('donationAmount')->getData(),
                    ],
                    'tax_rates' => [$this->taxRateId],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $this->generateUrl('job_donation_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'metadata' => [
                    'jobId' => (string) $job->getId(),
                ],
                'payment_intent_data' => [
                    'metadata' => [
                        'jobId' => (string) $job->getId(),
                    ],
                ],
                'tax_id_collection' => [
                    'enabled' => true,
                ],
            ]);

            return $this->redirect($session->url, 303);
        }

        return $this->renderForm('job/new.html.twig', [
            'job' => $job,
            'form' => $form,
        ]);
    }

    #[Route('/job/{id}/donation/success', name: 'job_donation_success', methods: ['GET'])]
    public function jobDonationSuccess(Job $job, Request $request, EntityManagerInterface $em, string $stripeApiKey): Response
    {
        Stripe::setApiKey($stripeApiKey);
        $session = Session::retrieve($request->get('session_id'));

        if (Session::PAYMENT_STATUS_PAID === $session->payment_status) {
            $job->pinUntil($job->getCreatedAt()->modify('+1 month'));
            $em->flush();
        }

        return $this->render('job/donation_success.html.twig');
    }

    #[Route('/job/donation/cancel', name: 'job_donation_cancel', methods: ['GET'])]
    public function jobDonationCancel(): Response
    {
        return $this->render('job/donation_cancel.html.twig');
    }

    #[Route('/job/{id}', name: 'job', methods: ['GET'])]
    public function job(Job $job, EntityManagerInterface $entityManager): RedirectResponse
    {
        $job->clicked();
        $entityManager->flush();

        return $this->redirect($job->getUrl());
    }

    public function subscriptionForm(): Response
    {
        $form = $this->createForm(SubscriptionType::class);

        return $this->render('job/_subscription_form.html.twig', [
           'form' => $form->createView(),
        ]);
    }

    #[Route('/subscribe', name: 'subscribe', methods: ['POST'])]
    public function subscribe(Request $request, SubscribeMailingListCommandHandler $handler, string $mailjetListId): Response
    {
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
}
