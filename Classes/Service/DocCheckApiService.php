<?php

declare(strict_types=1);

namespace Doc2k\DoccheckAccess\Service;

use Doc2k\DoccheckAccess\ValueObject\TokenResponse;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class DocCheckApiService
{
    /**
     * @param array<string, mixed> $configuration
     */
    public function buildAuthorizationUrl(array $configuration = []): string
    {
        $authorizationEndpoint = (string)($configuration['authorizationEndpoint'] ?? 'https://auth.doccheck.com/de/authorize');
        $clientId = (string)($configuration['clientId'] ?? '');
        $redirectUri = (string)($configuration['redirectUri'] ?? '');

        return $authorizationEndpoint
            . '?grant_type=authorization_code'
            . '&response_type=code'
            . '&client_id=' . rawurlencode($clientId)
            . '&redirect_uri=' . rawurlencode($redirectUri);
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function exchangeCodeForToken(string $code, array $configuration = []): TokenResponse
    {
        $tokenEndpoint = (string)($configuration['tokenEndpoint'] ?? 'https://auth.doccheck.com/token');
        $clientId = (string)($configuration['clientId'] ?? '');
        $clientSecret = (string)($configuration['clientSecret'] ?? '');
        $redirectUri = (string)($configuration['callbackPath'] ?? '');

        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);

        $response = $requestFactory->request(
            $tokenEndpoint,
            'POST',
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri' => $redirectUri,
                ],
            ]
        );

        $body = (string)$response->getBody();
        $data = json_decode($body, true);

        if (!is_array($data) || empty($data['access_token'])) {
            throw new \RuntimeException('DocCheck token exchange failed.', 1718800001);
        }

        return new TokenResponse(
            (string)($data['access_token'] ?? ''),
            (string)($data['token_type'] ?? ''),
            isset($data['expires_in']) ? (int)$data['expires_in'] : null,
            isset($data['refresh_token']) ? (string)$data['refresh_token'] : null,
            isset($data['scope']) ? (string)$data['scope'] : null,
            $data
        );
    }

    /**
     * Reserved for OAuth providers supporting state validation.
     * DocCheck Access Basic currently does not support state.
     */
    public function validateState(string $state, string $expectedState): bool
    {
        if ($state === '' || $expectedState === '') {
            return false;
        }

        return hash_equals($expectedState, $state);
    }
}
