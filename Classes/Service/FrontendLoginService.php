<?php

declare(strict_types=1);

namespace Doc2k\DoccheckAccess\Service;

use Doc2k\DoccheckAccess\ValueObject\TokenResponse;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use Psr\Http\Message\ServerRequestInterface;
use Doc2k\DoccheckAccess\Service\ConfigurationService;

final class FrontendLoginService
{
    private ConfigurationService $configurationService;

    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    public function loginFrontendUser(TokenResponse $tokenResponse, ServerRequestInterface $request): bool
    {
        if ($tokenResponse->getAccessToken() === '') {
            return false;
        }

        $frontendUser = $request->getAttribute('frontend.user');
        if (!$frontendUser instanceof FrontendUserAuthentication) {
            return false;
        }
        $userId = $this->configurationService->getFrontendUserUid();
        if ($userId <= 0) {
            return false;
        }

        $userRecord = $this->fetchFrontendUser($userId);
        if ($userRecord === []) {
            return false;
        }

        $frontendUser->createUserSession($userRecord);
        $frontendUser->user = $userRecord;
        $frontendUser->fetchGroupData();

        return true;
    }

    private function getFrontendUser(): ?FrontendUserAuthentication
    {
        $typoScriptFrontendController = $GLOBALS['TSFE'] ?? null;

        if (
            is_object($typoScriptFrontendController)
            && isset($typoScriptFrontendController->fe_user)
            && $typoScriptFrontendController->fe_user instanceof FrontendUserAuthentication
        ) {
            return $typoScriptFrontendController->fe_user;
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchFrontendUser(int $userUid): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('fe_users');

        $row = $queryBuilder
            ->select('*')
            ->from('fe_users')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($userUid, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'disable',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'deleted',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                )
            )
            ->executeQuery()
            ->fetchAssociative();

        return is_array($row) ? $row : [];
    }
}