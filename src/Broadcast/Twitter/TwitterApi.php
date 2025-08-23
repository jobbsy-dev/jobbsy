<?php

namespace App\Broadcast\Twitter;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class TwitterApi
{
    private const string BASE_URL = 'https://api.twitter.com/2';

    public function __construct(
        #[Autowire('%env(TWITTER_API_KEY)%')]
        private string $consumerKey,
        #[Autowire('%env(TWITTER_API_KEY_SECRET)%')]
        private string $consumerSecret,
        #[Autowire('%env(TWITTER_ACCESS_TOKEN)%')]
        private string $accessToken,
        #[Autowire('%env(TWITTER_ACCESS_TOKEN_SECRET)%')]
        private string $tokenSecret,
        private HttpClientInterface $httpClient,
    ) {
    }

    public function createTweet(Tweet $tweet): string
    {
        $method = 'POST';
        $payload = $tweet->toArray();
        $url = \sprintf('%s/tweets', self::BASE_URL);
        $authorizationHeader = $this->buildAuthorizationHeader(method: $method, url: $url);

        $response = $this->httpClient->request($method, $url, [
            'headers' => [
                'Authorization' => $authorizationHeader,
            ],
            'json' => $payload,
        ]);

        if (201 !== $response->getStatusCode()) {
            throw new \Exception('Unexpected response status code.');
        }

        $content = $response->toArray();

        if (false === isset($content['data']['id'])) {
            throw new \Exception('Unexpected response content.');
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
            'oauth_nonce' => md5((string) random_int(0, mt_getrandmax())),
            'oauth_version' => '1.0',
        ];

        ksort($oauthParameters);

        $return = [];

        /**
         * @var string $key
         * @var string $value
         */
        foreach ($oauthParameters as $key => $value) {
            $return[] = rawurlencode($key).'='.rawurlencode($value);
        }

        $signature = $method.'&'.rawurlencode($url).'&'.rawurlencode(implode('&', $return));

        $compositeKey = rawurlencode($this->consumerSecret).'&'.rawurlencode($this->tokenSecret);
        $oauth_signature = base64_encode(hash_hmac('sha1', $signature, $compositeKey, true));
        $oauthParameters['oauth_signature'] = $oauth_signature;

        $return = 'OAuth ';
        $values = [];
        /**
         * @var string $key
         * @var string $value
         */
        foreach ($oauthParameters as $key => $value) {
            $values[] = $key.'="'.rawurlencode($value).'"';
        }

        return $return.implode(', ', $values);
    }

    public function deleteTweet(string $tweetId): bool
    {
        $method = 'DELETE';
        $url = \sprintf('%s/tweets/%s', self::BASE_URL, $tweetId);
        $authorizationHeader = $this->buildAuthorizationHeader(method: $method, url: $url);

        $response = $this->httpClient->request($method, $url, [
            'headers' => [
                'Authorization' => $authorizationHeader,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Unexpected response status code.');
        }

        $content = $response->toArray();

        if (false === isset($content['data']['deleted'])) {
            throw new \Exception('Unexpected response content.');
        }

        return $content['data']['deleted'];
    }
}
