<?php

namespace App\Donation\Command;

use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

final readonly class CreateDonationPaymentUrlCommand
{
    public function __construct(
        public UuidInterface $jobId,
        public int $amount,
        public string $redirectSuccessUrl,
        public string $redirectCancelUrl,
    ) {
        Assert::greaterThan($amount, 0);
        Assert::notEmpty($redirectSuccessUrl);
        Assert::notEmpty($redirectCancelUrl);
    }
}
