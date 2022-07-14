<?php

namespace App\Subscription;

use App\Mailjet\MailjetApi;

class MailjetSubscriptionAdapter implements SubscriptionMailingListInterface
{
    public function __construct(private readonly MailjetApi $api)
    {
    }

    public function subscribe(string $email, string $mailingList): void
    {
        $contact = $this->api->createContact($email);

        if (null === $contact) {
            return;
        }

        if (empty($contact)) {
            return;
        }

        if (false === isset($contact[0]['ID'])) {
            return;
        }

        $this->api->addContactToList(
            $contact[0]['ID'],
            $mailingList
        );
    }
}
