<?php

namespace App\Mailjet\Model\SendCampaignDraft;

final class SendCampaignDraftRequest
{
    public function __construct(
        public readonly int $campaignId,
    ) {
    }
}
