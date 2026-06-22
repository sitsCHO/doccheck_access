<?php

declare(strict_types=1);

namespace Doc2k\DoccheckAccess\Middleware;

use Doc2k\DoccheckAccess\Service\ConfigurationService;
use Doc2k\DoccheckAccess\Service\DocCheckApiService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

final class LoginMiddleware implements MiddlewareInterface
{
    private const SESSION_KEY = 'doccheck_access';

    private DocCheckApiService $docCheckApiService;
    private ConfigurationService $configurationService;

    public function __construct(
        DocCheckApiService $docCheckApiService,
        ConfigurationService $configurationService
    ) {
        $this->docCheckApiService = $docCheckApiService;
        $this->configurationService = $configurationService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = rtrim($request->getUri()->getPath(), '/') . '/';
        $loginPath = '/doccheck-access/login/';

        if (substr($path, -strlen($loginPath)) !== $loginPath) {
            return $handler->handle($request);
        }

        $queryParams = $request->getQueryParams();
        $contentElementUid = isset($queryParams['ce']) && is_numeric($queryParams['ce']) ? (int)$queryParams['ce'] : 0;
        if ($contentElementUid <= 0) {
            return new RedirectResponse($this->buildPageRedirectUrl($this->configurationService->getFailurePid()), 303);
        }

        $contentElement = $this->fetchContentElement($contentElementUid);
        $successPid = (int)($contentElement['tx_doccheckaccess_success_pid'] ?? 0);
        if ($successPid <= 0) {
            $successPid = $this->configurationService->getSuccessPid();
        }

        $language = $request->getAttribute('language');

        $languageId = $language instanceof SiteLanguage
            ? $language->getLanguageId()
            : 0;

        $languageCode = $language instanceof SiteLanguage
            ? $language->getTwoLetterIsoCode()
            : 'en';

        $docCheckLanguage = in_array($languageCode, ['de', 'en', 'fr', 'it', 'nl', 'es'], true)
            ? $languageCode
            : 'en';

        $this->storeSessionData($request, [
            'contentElementUid' => $contentElementUid,
            'successPid' => $successPid,
            'languageId' => $languageId,
            'languageCode' => $docCheckLanguage,
        ]);


        $configuration = $this->configurationService->getAll();
        $configuration['authorizationEndpoint'] = 'https://auth.doccheck.com/' . $docCheckLanguage . '/authorize';

        $authorizationUrl = $this->docCheckApiService->buildAuthorizationUrl(
            $configuration
        );

        return new RedirectResponse($authorizationUrl, 303);
    }

    /**
     * @return array<string, mixed>
     */
    private function fetchContentElement(int $contentElementUid): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $row = $queryBuilder
            ->select('uid', 'tx_doccheckaccess_success_pid')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($contentElementUid, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'CType',
                    $queryBuilder->createNamedParameter('doccheckaccess_login')
                )
            )
            ->executeQuery()
            ->fetchAssociative();

        return is_array($row) ? $row : [];
    }

    /**
     * @param array<string, mixed> $data
     */
    private function storeSessionData(ServerRequestInterface $request, array $data): void
    {
        $frontendUser = $this->getFrontendUser($request);
        if (!$frontendUser instanceof FrontendUserAuthentication) {
            return;
        }

        $frontendUser->setKey('ses', self::SESSION_KEY, $data);
        $frontendUser->storeSessionData();
    }

    private function getFrontendUser(ServerRequestInterface $request): ?FrontendUserAuthentication
    {
        $frontendUser = $request->getAttribute('frontend.user');
        if ($frontendUser instanceof FrontendUserAuthentication) {
            return $frontendUser;
        }

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

    private function buildPageRedirectUrl(int $pageUid): string
    {
        if ($pageUid <= 0) {
            return '/';
        }

        try {
            $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($pageUid);
            return (string)$site->getRouter()->generateUri($pageUid);
        } catch (\Throwable $e) {
            return '/?id=' . $pageUid;
        }
    }
}
