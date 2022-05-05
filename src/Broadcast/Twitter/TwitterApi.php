<?php

namespace App\Broadcast\Twitter;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TwitterApi
{
    public function __construct(
        private readonly string $consumerKey,
        private readonly string $consumerSecret,
        private readonly string $accessToken,
        private readonly string $tokenSecret,
        private ?HttpClientInterface $httpClient = null
    ) {
        if (null === $this->httpClient) {
            $this->httpClient = HttpClient::create();
        }
    }

    public function createTweet(Tweet $tweet): array
    {
        $method = 'POST';
        $url = 'https://api.twitter.com/2/tweets';
        $payload = $tweet->toArray();
        $authorizationHeader = $this->buildAuthorizationHeader(method: $method, url: $url);

        $response = $this->httpClient->request($method, $url, [
            'headers' => [
                'Authorization' => $authorizationHeader,
            ],
            'json' => $payload
        ]);

        if (201 !== $response->getStatusCode()) {
            throw new \Exception('Unexpected response status code');
        }

        return $response->toArray();
    }

    private function buildAuthorizationHeader(string $method, string $url): string
    {
        $oauthParameters = [
            'oauth_consumer_key' => $this->consumerKey,
            'oauth_token' => $this->accessToken,
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_nonce' => md5(mt_rand()),
            'oauth_version' => '1.0',
        ];

        ksort($oauthParameters);

        $return = [];
        foreach ($oauthParameters as $key => $value) {
            $return[] = rawurlencode($key) . '=' . rawurlencode($value);
        }

        $signature = $method . "&" . rawurlencode($url) . '&' . rawurlencode(implode('&', $return));

        $compositeKey = rawurlencode($this->consumerSecret) . '&' . rawurlencode($this->tokenSecret);
        $oauth_signature = base64_encode(hash_hmac('sha1', $signature, $compositeKey, true));
        $oauthParameters['oauth_signature'] = $oauth_signature;

        $return = 'OAuth ';
        $values = array();
        foreach ($oauthParameters as $key => $value) {
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        }

        $return .= implode(', ', $values);

        return $return;
    }
}
