<?php

namespace App\Donation\Command;

use App\Donation\Command\CreateDonationPaymentUrlCommand;
use App\Donation\CreatePaymentUrlInterface;
use App\Repository\JobRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateDonationPaymentUrlCommandHandler
{
    public function __construct(
        private JobRepository $jobRepository,
        private CreatePaymentUrlInterface $createPaymentUrl,
    ) {
    }

    public function __invoke(CreateDonationPaymentUrlCommand $command): string
    {
        $job = $this->jobRepository->get($command->jobId);

        return ($this->createPaymentUrl)(
            job: $job,
            amount: $command->amount,
            redirectSuccessUrl: $command->redirectSuccessUrl,
            redirectCancelUrl: $command->redirectCancelUrl
        );
    }
}
