<?php

declare(strict_types=1);

namespace Doc2k\DoccheckAccess\ValueObject;

final class TokenResponse
{
    private string $accessToken;
    private string $tokenType;
    private ?int $expiresIn;
    private ?string $refreshToken;
    private ?string $scope;
    /**
     * @var array<string, mixed>
     */
    private array $rawResponse;

    /**
     * @param array<string, mixed> $rawResponse
     */
    public function __construct(
        string $accessToken = '',
        string $tokenType = '',
        ?int $expiresIn = null,
        ?string $refreshToken = null,
        ?string $scope = null,
        array $rawResponse = []
    ) {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->expiresIn = $expiresIn;
        $this->refreshToken = $refreshToken;
        $this->scope = $scope;
        $this->rawResponse = $rawResponse;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getExpiresIn(): ?int
    {
        return $this->expiresIn;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRawResponse(): array
    {
        return $this->rawResponse;
    }

    public function isValid(): bool
    {
        return $this->accessToken !== '';
    }
}
