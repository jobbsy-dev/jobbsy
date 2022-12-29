<?php

namespace App\Mailjet\Model\TestCampaignDraft;

final class Recipient
{
    public function __construct(public string $email, public ?string $name = null)
    {
    }
}
