<?php

namespace App\Provider;

use Webmozart\Assert\Assert;

final readonly class AccessToken
{
    private function __construct(private string $token, private int $expiresIn, private \DateTimeImmutable $createdAt)
    {
        Assert::notEmpty($token);
        Assert::greaterThan($this->expiresIn, 0);
    }

    public static function create(
        string $token,
        int $expiresIn,
        \DateTimeImmutable $createAt = new \DateTimeImmutable()
    ): self {
        return new self($token, $expiresIn, $createAt);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function hasExpired(): bool
    {
        return new \DateTimeImmutable() > $this->createdAt->add(new \DateInterval(sprintf('PT%sS', $this->expiresIn)));
    }
}
