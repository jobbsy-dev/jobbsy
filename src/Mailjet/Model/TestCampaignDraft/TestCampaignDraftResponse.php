<?php

namespace App\Mailjet\Model\TestCampaignDraft;

final readonly class TestCampaignDraftResponse
{
    private function __construct(public int $count, public array $data, public int $total)
    {
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
