<?php

namespace App\Mailjet\Model\CreateCampaignDraftContent;

final readonly class CreateCampaignDraftContentResponse
{
    private function __construct(
        public int $count,
        public array $data,
        public int $total,
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
