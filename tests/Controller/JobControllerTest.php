<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\EmploymentType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JobControllerTest extends WebTestCase
{
    public function testListJobsOffers(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('a.btn.btn-primary', 'Post a Job');

        self::assertSame(
            'Symfony developer Remote',
            $crawler->filter('.list-group-item p.h5')->eq(0)->text(),
            'Pinned job'
        );
        self::assertSame(
            'Backend Symfony developer',
            $crawler->filter('.list-group-item p.h5')->eq(1)->text()
        );
    }

    public function testCreateJobOffer(): void
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
        ]);
        self::assertResponseRedirects('/');
        $crawler = $client->followRedirect();

        self::assertSame(
            'Symfony freelance developer',
            $crawler->filter('.list-group-item p.h5')->eq(1)->text()
        );
    }

    public function testJobRedirect(): void
    {
        $client = static::createClient();
        $client->request('GET', '/job/'.AppFixtures::JOB_1_ID);
        self::assertResponseRedirects('https://example.com');
    }
}
