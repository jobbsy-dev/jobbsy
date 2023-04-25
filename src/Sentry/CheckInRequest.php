<?php

namespace App\Sentry;

final readonly class CheckInRequest
{
    public function __construct(public string $monitorSlug, public CheckInStatus $status)
    {
    }

    public static function createInProgress(string $monitorSlug): self
    {
        return new self(
            $monitorSlug,
            CheckInStatus::inProgress,
        );
    }

    public static function createOk(string $monitorSlug): self
    {
        return new self(
            $monitorSlug,
            CheckInStatus::ok,
        );
    }

    public static function createError(string $monitorSlug): self
    {
        return new self(
            $monitorSlug,
            CheckInStatus::error,
        );
    }

    public function payload(): array
    {
        return [
            'status' => $this->status->value,
        ];
    }
}
