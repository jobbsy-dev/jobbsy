<?php

namespace App\Mailjet;

use App\Mailjet\Model\CreateCampaignDraft\CreateCampaignDraftRequest;
use App\Mailjet\Model\CreateCampaignDraft\CreateCampaignDraftResponse;
use App\Mailjet\Model\CreateCampaignDraftContent\CreateCampaignDraftContentRequest;
use App\Mailjet\Model\CreateCampaignDraftContent\CreateCampaignDraftContentResponse;
use App\Mailjet\Model\ManageContact\ManageContactRequest;
use App\Mailjet\Model\ManageContact\ManageContactResponse;
use App\Mailjet\Model\SendCampaignDraft\SendCampaignDraftRequest;
use App\Mailjet\Model\SendCampaignDraft\SendCampaignDraftResponse;
use App\Mailjet\Model\TestCampaignDraft\TestCampaignDraftRequest;
use App\Mailjet\Model\TestCampaignDraft\TestCampaignDraftResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class MailjetApi
{
    public function __construct(private HttpClientInterface $mailjetClient)
    {
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

    public function createCampaignDraft(
        CreateCampaignDraftRequest $createCampaignDraftRequest,
    ): ?CreateCampaignDraftResponse {
        $response = $this->mailjetClient->request('POST', 'campaigndraft', [
            'json' => $createCampaignDraftRequest->toArray(),
        ]);

        if (201 !== $response->getStatusCode()) {
            return null;
        }

        $data = $response->toArray(false);

        return CreateCampaignDraftResponse::fromArray($data);
    }

    public function createCampaignDraftContent(
        CreateCampaignDraftContentRequest $createCampaignDraftContentRequest,
    ): ?CreateCampaignDraftContentResponse {
        $url = \sprintf('campaigndraft/%d/detailcontent', $createCampaignDraftContentRequest->campaignId);
        $response = $this->mailjetClient->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => $createCampaignDraftContentRequest->payload(),
        ]);

        if (201 !== $response->getStatusCode()) {
            return null;
        }

        $data = $response->toArray(false);

        return CreateCampaignDraftContentResponse::fromArray($data);
    }

    public function sendCampaignDraft(SendCampaignDraftRequest $sendCampaignDraftRequest): ?SendCampaignDraftResponse
    {
        $url = \sprintf('campaigndraft/%d/send', $sendCampaignDraftRequest->campaignId);
        $response = $this->mailjetClient->request('POST', $url);

        if (201 !== $response->getStatusCode()) {
            return null;
        }

        $data = $response->toArray(false);

        return SendCampaignDraftResponse::fromArray($data);
    }

    public function manageContact(ManageContactRequest $manageContactRequest): ?ManageContactResponse
    {
        $url = \sprintf('contactslist/%s/managecontact', $manageContactRequest->contactListId);
        $response = $this->mailjetClient->request('POST', $url, [
            'json' => $manageContactRequest->toArray(),
        ]);

        if (201 !== $response->getStatusCode()) {
            return null;
        }

        $data = $response->toArray(false);

        return ManageContactResponse::fromArray($data);
    }

    public function testCampaignDraft(TestCampaignDraftRequest $request): ?TestCampaignDraftResponse
    {
        $url = \sprintf('campaigndraft/%d/test', $request->draftId);
        $response = $this->mailjetClient->request('POST', $url, [
            'json' => $request->toArray(),
        ]);

        if (201 !== $response->getStatusCode()) {
            return null;
        }

        $data = $response->toArray(false);

        return TestCampaignDraftResponse::fromArray($data);
    }
}
