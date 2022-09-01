<?php

namespace App\Donation\Command;

use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

final class CreateDonationPaymentUrlCommand
{
    public function __construct(
        public readonly UuidInterface $jobId,
        public readonly int $amount,
        public readonly string $redirectSuccessUrl,
        public readonly string $redirectCancelUrl,
    ) {
        Assert::greaterThan($amount, 0);
        Assert::notEmpty($redirectSuccessUrl);
        Assert::notEmpty($redirectCancelUrl);
    }
}
