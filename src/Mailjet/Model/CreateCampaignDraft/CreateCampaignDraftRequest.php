<?php

namespace App\Mailjet\Model\CreateCampaignDraft;

final readonly class CreateCampaignDraftRequest
{
    public function __construct(
        public string $title,
        public int $contactsListId,
        public string $locale,
        public string $senderEmail,
        public string $senderName,
        public string $subject,
        public string $sender
    ) {
    }

    public function toArray(): array
    {
        return [
            'Title' => $this->title,
            'ContactsListID' => $this->contactsListId,
            'Locale' => $this->locale,
            'SenderEmail' => $this->senderEmail,
            'SenderName' => $this->senderName,
            'Subject' => $this->subject,
            'Sender' => $this->sender,
        ];
    }
}
