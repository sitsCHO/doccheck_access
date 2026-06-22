<?php

declare(strict_types=1);

namespace Doc2k\DoccheckAccess\Service;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

final class ConfigurationService
{
    private ExtensionConfiguration $extensionConfiguration;

    public function __construct(ExtensionConfiguration $extensionConfiguration)
    {
        $this->extensionConfiguration = $extensionConfiguration;
    }

    public function getClientId(): string
    {
        return $this->getStringValue('clientId');
    }

    public function getClientSecret(): string
    {
        return $this->getStringValue('clientSecret');
    }

    public function getCallbackPath(): string
    {
        return $this->getStringValue('callbackPath', '/doccheck-access/callback/');
    }

    public function getSuccessPid(): int
    {
        return $this->getIntegerValue('successPid');
    }

    public function getFailurePid(): int
    {
        return $this->getIntegerValue('failurePid');
    }

    public function getFrontendUserUid(): int
    {
        return $this->getIntegerValue('frontendUserUid');
    }

    public function getFrontendUserGroupUid(): int
    {
        return $this->getIntegerValue('frontendUserGroupUid');
    }

    /**
     * @return array<string, mixed>
     */
    public function getAll(): array
    {
        try {
            $configuration = $this->extensionConfiguration->get('doccheck_access');
        } catch (\Throwable $exception) {
            return [];
        }

        return is_array($configuration) ? $configuration : [];
    }

    private function getStringValue(string $key, string $default = ''): string
    {
        $value = $this->getAll()[$key] ?? $default;

        return is_scalar($value) ? (string)$value : $default;
    }

    private function getIntegerValue(string $key, int $default = 0): int
    {
        $value = $this->getAll()[$key] ?? $default;

        return is_numeric($value) ? (int)$value : $default;
    }
}
