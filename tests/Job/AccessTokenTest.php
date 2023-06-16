<?php

namespace App\Tests\Job;

use App\Job\AccessToken;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class AccessTokenTest extends TestCase
{
    public function test_create_empty_access_token_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AccessToken::create('', 2);
    }

    public function test_create_access_token_with_negative_expiration_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AccessToken::create('xxx', -5);
    }

    public function test_access_token_has_expired(): void
    {
        $accessToken = AccessToken::create('xxx', 1);
        sleep(2);

        self::assertTrue($accessToken->hasExpired());
    }

    public function test_access_token_has_not_expired(): void
    {
        $accessToken = AccessToken::create('xxx', 1500);

        self::assertFalse($accessToken->hasExpired());
    }
}
