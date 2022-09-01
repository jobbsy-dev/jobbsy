<?php

namespace App\Donation;

use App\Entity\Job;

interface CreatePaymentUrlInterface
{
    public function __invoke(Job $job, int $amount, string $redirectSuccessUrl, string $redirectCancelUrl): string;
}
