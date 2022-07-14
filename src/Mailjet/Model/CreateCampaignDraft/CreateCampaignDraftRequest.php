<?php

namespace App\Mailjet\Model\CreateCampaignDraft;

final class CreateCampaignDraftRequest
{
    public function __construct(
        readonly string $title,
        readonly int $contactListId,
        readonly string $locale,
        readonly string $senderEmail,
        readonly string $senderName,
        readonly string $subject,
        readonly int $sender
    ) {
    }

    public function toArray(): array
    {
        return [
            'Title' => $this->title,
            'ContactsListID' => $this->contactListId,
            'Locale' => $this->locale,
            'SenderEmail' => $this->senderEmail,
            'SenderName' => $this->senderName,
            'Subject' => $this->subject,
            'Sender' => $this->sender,
        ];
    }
}
