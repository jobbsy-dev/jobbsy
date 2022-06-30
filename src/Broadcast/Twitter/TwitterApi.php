<?php

namespace App\Broadcast\Twitter;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TwitterApi
{
    public function __construct(
        #[Autowire('%env(TWITTER_API_KEY)%')]
        private readonly string $consumerKey,
        #[Autowire('%env(TWITTER_API_KEY_SECRET)%')]
        private readonly string $consumerSecret,
        #[Autowire('%env(TWITTER_ACCESS_TOKEN)%')]
        private readonly string $accessToken,
        #[Autowire('%env(TWITTER_ACCESS_TOKEN_SECRET)%')]
        private readonly string $tokenSecret,
        private ?HttpClientInterface $httpClient = null
    ) {
        if (null === $this->httpClient) {
            $this->httpClient = HttpClient::create();
        }
    }

    public function createTweet(Tweet $tweet): string
    {
        $method = 'POST';
        $url = 'https://api.twitter.com/2/tweets';
        $payload = $tweet->toArray();
        $authorizationHeader = $this->buildAuthorizationHeader(method: $method, url: $url);

        $response = $this->httpClient->request($method, $url, [
            'headers' => [
                'Authorization' => $authorizationHeader,
            ],
            'json' => $payload,
        ]);

        if (201 !== $response->getStatusCode()) {
            throw new \Exception('Unexpected response status code');
        }

        $content = $response->toArray();

        if (false === isset($content['data']['id'])) {
            throw new \Exception('Unexpected response content');
        }

        return $content['data']['id'];
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
            $return[] = rawurlencode($key).'='.rawurlencode($value);
        }

        $signature = $method.'&'.rawurlencode($url).'&'.rawurlencode(implode('&', $return));

        $compositeKey = rawurlencode($this->consumerSecret).'&'.rawurlencode($this->tokenSecret);
        $oauth_signature = base64_encode(hash_hmac('sha1', $signature, $compositeKey, true));
        $oauthParameters['oauth_signature'] = $oauth_signature;

        $return = 'OAuth ';
        $values = [];
        foreach ($oauthParameters as $key => $value) {
            $values[] = "$key=\"".rawurlencode($value).'"';
        }

        $return .= implode(', ', $values);

        return $return;
    }

    public function deleteTweet(string $tweetId): bool
    {
        $method = 'DELETE';
        $url = 'https://api.twitter.com/2/tweets/'.$tweetId;
        $authorizationHeader = $this->buildAuthorizationHeader(method: $method, url: $url);

        $response = $this->httpClient->request($method, $url, [
            'headers' => [
                'Authorization' => $authorizationHeader,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Unexpected response status code');
        }

        $content = $response->toArray();

        if (false === isset($content['data']['deleted'])) {
            throw new \Exception('Unexpected response content');
        }

        return $content['data']['deleted'];
    }
}
