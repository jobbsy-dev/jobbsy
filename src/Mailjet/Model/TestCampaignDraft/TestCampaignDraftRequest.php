<?php

namespace App\Mailjet\Model\TestCampaignDraft;

final readonly class TestCampaignDraftRequest
{
    /**
     * @param Recipient[] $recipients
     */
    public function __construct(public string $draftId, public array $recipients)
    {
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
