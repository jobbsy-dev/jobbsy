<?php

namespace App\Mailjet\Model\ManageContact;

final class ManageContactRequest
{
    public function __construct(
        public readonly int $contactListId,
        public readonly Action $action,
        public readonly string $email,
        public readonly ?string $name = null,
    ) {
    }

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
