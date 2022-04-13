<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\JobType;
use App\Form\SubscriptionType;
use App\Repository\JobRepository;
use App\Subscription\SubscribeMailingListCommand;
use App\Subscription\SubscribeMailingListCommandHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class JobController extends AbstractController
{
    #[
        Route('/', name: 'job_index', defaults: ['_format' => 'html'], methods: ['GET']),
        Route('/rss.xml', name: 'job_rss', defaults: ['_format' => 'xml'], methods: ['GET']),
    ]
    public function index(JobRepository $jobRepository, string $_format): Response
    {
        return $this->render('job/index.'.$_format.'.twig', [
            'jobs' => $jobRepository->findBy([], ['createdAt' => 'DESC'], 30),
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

            return $this->redirectToRoute('job_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('job/new.html.twig', [
            'job' => $job,
            'form' => $form,
        ]);
    }

    #[Route('/job/{id}', name: 'job', methods: ['GET'])]
    public function job(Job $job): RedirectResponse
    {
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
