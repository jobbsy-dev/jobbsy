<?php

namespace App\Subscription;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MailjetApi
{
    public function __construct(
        string $mailjetApiKey,
        string $mailjetApiSecretKey,
        private ?HttpClientInterface $mailjetClient = null
    ) {
        if (null === $mailjetClient) {
            $this->mailjetClient = HttpClient::create([
                'base_uri' => 'https://api.mailjet.com/v3/REST/',
                'auth_basic' => [$mailjetApiKey, $mailjetApiSecretKey],
            ]);
        }
    }

    public function createContact(string $email): ?array
    {
        $response = $this->mailjetClient->request('POST', 'contact', [
            'json' => [
                'Email' => $email,
            ],
        ]);

        if (201 !== $response->getStatusCode()) {
            return null;
        }

        $data = $response->toArray(false);

        if (false === isset($data['Data'])) {
            return null;
        }

        return $data['Data'];
    }

    public function addContactToList(string $contactId, string $listId): ?array
    {
        $response = $this->mailjetClient->request('POST', 'listrecipient', [
            'json' => [
                'ContactID' => $contactId,
                'ListID' => $listId,
            ],
        ]);

        if (201 !== $response->getStatusCode()) {
            return null;
        }

        $data = $response->toArray(false);

        if (false === isset($data['Data'])) {
            return null;
        }

        return $data['Data'];
    }
}
