<?php

namespace App\Mailjet\Model\ManageContact;

final readonly class ManageContactRequest
{
    public function __construct(
        public int $contactListId,
        public Action $action,
        public string $email,
        public ?string $name = null,
        ) {
    }

    /**
     * @return array{Action: string, Email: string, Name?: string}
     */
    public function toArray(): array
    {
        $payload = [
            'Action' => $this->action->value,
            'Email' => $this->email,
        ];

        if (null !== $this->name) {
            $payload['Name'] = $this->name;
        }

        return $payload;
    }
}
