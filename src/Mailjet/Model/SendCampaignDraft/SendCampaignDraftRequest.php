<?php

namespace App\Mailjet\Model\SendCampaignDraft;

final readonly class SendCampaignDraftRequest
{
    public function __construct(public int $campaignId)
    {
    }
}
