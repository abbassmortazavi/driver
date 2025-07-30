<?php

namespace App\Dto\Auth;

readonly class AccessTokenDto
{
    public function __construct(
        protected string $accessToken,
        protected string $refreshToken,
        protected int $expiresIn,
        protected string $tokenType,
    ) {}

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }
}
