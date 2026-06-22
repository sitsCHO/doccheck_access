<?php

declare(strict_types=1);


namespace Doc2k\DoccheckAccess\Middleware;

use Doc2k\DoccheckAccess\Service\ConfigurationService;
use Doc2k\DoccheckAccess\Service\DocCheckApiService;
use Doc2k\DoccheckAccess\Service\FrontendLoginService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class CallbackMiddleware implements MiddlewareInterface
{
    private const SESSION_KEY = 'doccheck_access';

    private DocCheckApiService $docCheckApiService;
    private FrontendLoginService $frontendLoginService;
    private ConfigurationService $configurationService;

    public function __construct(
        DocCheckApiService $docCheckApiService,
        FrontendLoginService $frontendLoginService,
        ConfigurationService $configurationService
    ) {
        $this->docCheckApiService = $docCheckApiService;
        $this->frontendLoginService = $frontendLoginService;
        $this->configurationService = $configurationService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() !== '/doccheck-access/callback/') {
            return $handler->handle($request);
        }

        $queryParams = $request->getQueryParams();
        $code = isset($queryParams['code']) && is_scalar($queryParams['code']) ? (string)$queryParams['code'] : '';
        $sessionData = $this->getSessionData($request);
        $languageId = isset($sessionData['languageId']) && is_numeric($sessionData['languageId'])
            ? (int)$sessionData['languageId']
            : 0;

        if ($code === '') {
            return new RedirectResponse($this->buildPageRedirectUrl($this->configurationService->getFailurePid(), $languageId), 303);
        }

        $tokenResponse = $this->docCheckApiService->exchangeCodeForToken(
            $code,
            $this->configurationService->getAll()
        );

        $loginSucceeded = $this->frontendLoginService->loginFrontendUser(
            $tokenResponse,
            $request
        );



        if (!$loginSucceeded) {
            return new RedirectResponse($this->buildPageRedirectUrl($this->configurationService->getFailurePid(), $languageId), 303);
        }

        $successPid = isset($sessionData['successPid']) && is_numeric($sessionData['successPid'])
            ? (int)$sessionData['successPid']
            : $this->configurationService->getSuccessPid();

        return new RedirectResponse($this->buildPageRedirectUrl($successPid, $languageId), 303);
    }

    /**
     * @return array<string, mixed>
     */
    private function getSessionData(ServerRequestInterface $request): array
    {
        $frontendUser = $this->getFrontendUser($request);
        if (!$frontendUser instanceof FrontendUserAuthentication) {
            return [];
        }

        $sessionData = $frontendUser->getKey('ses', self::SESSION_KEY);

        return is_array($sessionData) ? $sessionData : [];
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

    private function buildPageRedirectUrl(int $pageUid, int $languageId = 0): string
    {
        if ($pageUid <= 0) {
            return '/';
        }

        try {
            $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($pageUid);

            return (string)$site->getRouter()->generateUri(
                $pageUid,
                ['_language' => $languageId]
            );
        } catch (\Throwable $e) {
            return '/?id=' . $pageUid . ($languageId > 0 ? '&L=' . $languageId : '');
        }
    }
}
