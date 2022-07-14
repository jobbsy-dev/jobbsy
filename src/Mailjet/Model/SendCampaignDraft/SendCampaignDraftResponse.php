<?php

namespace App\Mailjet\Model\SendCampaignDraft;

final class SendCampaignDraftResponse
{
    private function __construct(
        public readonly int $count,
        public readonly array $data,
        public readonly int $total,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['Count'],
            $data['Data'],
            $data['Total'],
        );
    }
}
