<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Entity\Job;
use App\Job\EmploymentType;
use App\Repository\JobRepository;
use App\Tests\Mock\MockStripeClient;
use Stripe\ApiRequestor;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class JobControllerTest extends WebTestCase
{
    public function test_list_jobs_offers(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('a.btn.btn-primary', 'Post a job');

        self::assertSame(
            'Symfony developer Remote',
            $crawler->filter('.card-job p.h5')->eq(0)->text(),
            'Pinned job'
        );
        self::assertSame(
            'Lead dev Symfony Paris',
            $crawler->filter('.card-job p.h5')->eq(1)->text()
        );
    }

    public function test_create_job_offer_without_donation(): void
    {
        $client = static::createClient();
        $client->request('GET', '/job/new');
        self::assertResponseIsSuccessful();

        $client->submitForm('Post', [
            'post_job_offer[title]' => 'Symfony freelance developer',
            'post_job_offer[location]' => 'Remote',
            'post_job_offer[employmentType]' => EmploymentType::CONTRACT->value,
            'post_job_offer[organization]' => 'Symfony',
            'post_job_offer[url]' => 'https://symfony.com',
            'post_job_offer[tags]' => 'symfony,freelance,sql',
            'post_job_offer[contactEmail]' => 'test@example.com',
        ]);
        self::assertResponseRedirects('/');
        $crawler = $client->followRedirect();

        self::assertSame(
            'Symfony freelance developer',
            $crawler->filter('.card-job p.h5')->eq(0)->text()
        );
    }

    public function test_create_job_offer_with_donation(): void
    {
        $this->markTestSkipped('Donation disabled');

        $client = static::createClient();
        $client->request('GET', '/job/new');
        self::assertResponseIsSuccessful();

        $mockStripeClient = new MockStripeClient(
            file_get_contents(__DIR__.'/../Mock/create_session.json')
        );
        ApiRequestor::setHttpClient($mockStripeClient);

        $client->submitForm('Post', [
            'post_job_offer[title]' => 'Symfony freelance developer',
            'post_job_offer[location]' => 'Remote',
            'post_job_offer[employmentType]' => EmploymentType::CONTRACT->value,
            'post_job_offer[organization]' => 'Symfony',
            'post_job_offer[url]' => 'https://symfony.com',
            'post_job_offer[tags]' => 'symfony,freelance,sql',
            'post_job_offer[donationAmount]' => 5000,
            'post_job_offer[contactEmail]' => 'test@example.com',
        ]);
        self::assertResponseRedirects('https://checkout.stripe.com/pay/xxx');
    }

    public function test_job_donation_success_with_unpaid_payment(): void
    {
        $mockStripeClient = new MockStripeClient(
            retrieveSessionResponse: file_get_contents(__DIR__.'/../Mock/retrieve_session_unpaid.json')
        );
        ApiRequestor::setHttpClient($mockStripeClient);

        $client = static::createClient();
        $client->request('GET', sprintf('/job/%s/donation/success?session_id=10', AppFixtures::JOB_2_ID));
        self::assertResponseIsSuccessful();

        /** @var JobRepository $jobRepository */
        $jobRepository = static::getContainer()->get(JobRepository::class);
        /** @var Job $job */
        $job = $jobRepository->find(AppFixtures::JOB_2_ID);

        self::assertFalse($job->isPinned());
    }

    public function test_job_donation_success_with_paid_payment(): void
    {
        $mockStripeClient = new MockStripeClient(
            retrieveSessionResponse: file_get_contents(__DIR__.'/../Mock/retrieve_session_paid.json')
        );
        ApiRequestor::setHttpClient($mockStripeClient);

        $client = static::createClient();
        $client->request('GET', sprintf('/job/%s/donation/success?session_id=10', AppFixtures::JOB_2_ID));
        self::assertResponseIsSuccessful();

        /** @var JobRepository $jobRepository */
        $jobRepository = static::getContainer()->get(JobRepository::class);
        /** @var Job $job */
        $job = $jobRepository->find(AppFixtures::JOB_2_ID);

        self::assertTrue($job->isPinned());
    }

    public function test_job_donation_cancelled(): void
    {
        $client = static::createClient();
        $client->request('GET', '/job/donation/cancel');
        self::assertResponseIsSuccessful('Donation cancelled');
    }

    public function test_job_redirect(): void
    {
        $client = static::createClient();
        $client->request('GET', '/job/'.AppFixtures::JOB_1_ID);
        self::assertResponseRedirects('https://example.com?ref=jobbsy');
    }

    public function test_sponsor_job(): void
    {
        $client = static::createClient();
        $client->request('GET', sprintf('/job/%s/sponsor', AppFixtures::JOB_1_ID));
        self::assertResponseIsSuccessful();

        $mockStripeClient = new MockStripeClient(
            file_get_contents(__DIR__.'/../Mock/create_session.json')
        );
        ApiRequestor::setHttpClient($mockStripeClient);

        $client->submitForm('Sponsor', [
            'sponsor[donationAmount]' => 5000,
        ]);
        self::assertResponseRedirects('https://checkout.stripe.com/pay/xxx');
    }
}
