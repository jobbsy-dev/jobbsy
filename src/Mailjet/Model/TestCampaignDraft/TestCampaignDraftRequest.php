<?php

namespace App\Mailjet\Model\TestCampaignDraft;

final class TestCampaignDraftRequest
{
    public function __construct(
        public readonly string $draftId,
        public readonly array $recipients,
    ) {
    }

    public function toArray(): array
    {
        $payload = [
            'Recipients' => [],
        ];

        foreach ($this->recipients as $recipient) {
            $payload['Recipients'][] = [
                'Email' => $recipient->email,
                'Name' => $recipient->name,
            ];
        }

        return $payload;
    }
}
