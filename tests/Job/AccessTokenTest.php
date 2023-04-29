<?php

namespace App\Tests\Job;

use App\Job\AccessToken;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

final class AccessTokenTest extends TestCase
{
    public function testCreateEmptyAccessTokenThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AccessToken::create('', 2);
    }

    public function testCreateAccessTokenWithNegativeExpirationThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AccessToken::create('xxx', -5);
    }

    public function testAccessTokenHasExpired(): void
    {
        $accessToken = AccessToken::create('xxx', 1);
        sleep(2);

        self::assertTrue($accessToken->hasExpired());
    }

    public function testAccessTokenHasNotExpired(): void
    {
        $accessToken = AccessToken::create('xxx', 1500);

        self::assertFalse($accessToken->hasExpired());
    }
}
