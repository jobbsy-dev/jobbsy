<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Job\EmploymentType;
use App\Repository\JobRepository;
use App\Tests\Mock\MockStripeClient;
use Stripe\ApiRequestor;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JobControllerTest extends WebTestCase
{
    public function testListJobsOffers(): void
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

    public function testCreateJobOfferWithoutDonation(): void
    {
        $client = static::createClient();
        $client->request('GET', '/job/new');
        self::assertResponseIsSuccessful();

        $client->submitForm('Post', [
            'job[title]' => 'Symfony freelance developer',
            'job[location]' => 'Remote',
            'job[employmentType]' => EmploymentType::CONTRACT->value,
            'job[organization]' => 'Symfony',
            'job[url]' => 'https://symfony.com',
            'job[tags]' => 'symfony,freelance,sql',
            'job[donationAmount]' => 0,
            'job[contactEmail]' => 'test@example.com',
        ]);
        self::assertResponseRedirects('/');
        $crawler = $client->followRedirect();

        self::assertSame(
            'Symfony freelance developer',
            $crawler->filter('.card-job p.h5')->eq(0)->text()
        );
    }

    public function testCreateJobOfferWithDonation(): void
    {
        $client = static::createClient();
        $client->request('GET', '/job/new');
        self::assertResponseIsSuccessful();

        $mockStripeClient = new MockStripeClient(
            file_get_contents(__DIR__.'/../Mock/create_session.json')
        );
        ApiRequestor::setHttpClient($mockStripeClient);

        $client->submitForm('Post', [
            'job[title]' => 'Symfony freelance developer',
            'job[location]' => 'Remote',
            'job[employmentType]' => EmploymentType::CONTRACT->value,
            'job[organization]' => 'Symfony',
            'job[url]' => 'https://symfony.com',
            'job[tags]' => 'symfony,freelance,sql',
            'job[donationAmount]' => 5000,
            'job[contactEmail]' => 'test@example.com',
        ]);
        self::assertResponseRedirects('https://checkout.stripe.com/pay/xxx');
    }

    public function testJobDonationSuccessWithUnpaidPayment(): void
    {
        $mockStripeClient = new MockStripeClient(
            retrieveSessionResponse: file_get_contents(__DIR__.'/../Mock/retrieve_session_unpaid.json')
        );
        ApiRequestor::setHttpClient($mockStripeClient);

        $client = static::createClient();
        $client->request('GET', sprintf('/job/%s/donation/success?session_id=10', AppFixtures::JOB_2_ID));
        self::assertResponseIsSuccessful();

        $job = static::getContainer()->get(JobRepository::class)->find(AppFixtures::JOB_2_ID);

        self::assertFalse($job->isPinned());
    }

    public function testJobDonationSuccessWithPaidPayment(): void
    {
        $mockStripeClient = new MockStripeClient(
            retrieveSessionResponse: file_get_contents(__DIR__.'/../Mock/retrieve_session_paid.json')
        );
        ApiRequestor::setHttpClient($mockStripeClient);

        $client = static::createClient();
        $client->request('GET', sprintf('/job/%s/donation/success?session_id=10', AppFixtures::JOB_2_ID));
        self::assertResponseIsSuccessful();

        $job = static::getContainer()->get(JobRepository::class)->find(AppFixtures::JOB_2_ID);

        self::assertTrue($job->isPinned());
    }

    public function testJobDonationCancelled(): void
    {
        $client = static::createClient();
        $client->request('GET', '/job/donation/cancel');
        self::assertResponseIsSuccessful('Donation cancelled');
    }

    public function testJobRedirect(): void
    {
        $client = static::createClient();
        $client->request('GET', '/job/'.AppFixtures::JOB_1_ID);
        self::assertResponseRedirects('https://example.com?ref=jobbsy');
    }

    public function testSponsorJob(): void
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
