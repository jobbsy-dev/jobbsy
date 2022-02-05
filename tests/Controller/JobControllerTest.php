<?php

namespace App\Tests\Controller;

use App\EmploymentType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JobControllerTest extends WebTestCase
{
    public function testListJobsOffers(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('a.btn.btn-outline-primary', 'Post a Job');

        self::assertSelectorTextContains('h5', 'Lead dev Symfony Paris');
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
        $client->followRedirect();

        self::assertSelectorTextContains('h5', 'Symfony freelance developer');
    }
}
