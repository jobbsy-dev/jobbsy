<?php

namespace App\Subscription;

use App\Mailjet\MailjetApi;
use App\Mailjet\Model\ManageContact\Action;
use App\Mailjet\Model\ManageContact\ManageContactRequest;

class MailjetSubscriptionAdapter implements SubscriptionMailingListInterface
{
    public function __construct(private readonly MailjetApi $api)
    {
    }

    public function subscribe(string $email, string $mailingList): void
    {
        $this->api->manageContact(new ManageContactRequest(
            $mailingList,
            Action::ADD_FORCE,
            $email,
        ));
    }
}
