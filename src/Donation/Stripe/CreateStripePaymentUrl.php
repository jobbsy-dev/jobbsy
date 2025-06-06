<?php

namespace App\Donation\Stripe;

use App\Donation\CreatePaymentUrlInterface;
use App\Entity\Job;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class CreateStripePaymentUrl implements CreatePaymentUrlInterface
{
    public function __construct(
        #[Autowire('%env(STRIPE_TAX_RATE_ID)%')]
        private string $taxRateId,
        #[Autowire('%env(STRIPE_API_KEY)%')]
        private string $stripeApiKey,
    ) {
    }

    public function __invoke(Job $job, int $amount, string $redirectSuccessUrl, string $redirectCancelUrl): string
    {
        Stripe::setApiKey($this->stripeApiKey);

        /** @var string $customerEmail */
        $customerEmail = $job->getContactEmail();

        $session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Sponsor job offer & open source',
                    ],
                    'unit_amount' => $amount,
                ],
                'tax_rates' => [$this->taxRateId],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $redirectSuccessUrl,
            'cancel_url' => $redirectCancelUrl,
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
            'customer_email' => $customerEmail,
        ]);

        if (null === ($url = $session->url)) {
            throw new \Exception('Unable to generate payment url.');
        }

        return $url;
    }
}
