<?php

declare(strict_types=1);

namespace Doc2k\DoccheckAccess\ViewHelpers;

use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class ErrorMessageViewHelper extends AbstractViewHelper
{
    private const ERROR_SESSION_KEY = 'doccheck_access_error';

    /**
     * @var array<string, string>
     */
    private const ERROR_MESSAGES = [
        'missing_code' => 'The DocCheck login was cancelled or no authorization code was returned.',
        'token_exchange_failed' => 'The DocCheck login could not be completed. Please try again later.',
        'frontend_login_failed' => 'The frontend login could not be created.',
        'missing_content_element' => 'The DocCheck login button is invalid.',
        'invalid_content_element' => 'The DocCheck login button configuration is invalid.',
    ];

    public function render(): string
    {
        $frontendUser = $this->getFrontendUser();
        if (!$frontendUser instanceof FrontendUserAuthentication) {
            return '';
        }

        $errorCode = $frontendUser->getKey('ses', self::ERROR_SESSION_KEY);
        if (!is_string($errorCode) || $errorCode === '') {
            return '';
        }

        $frontendUser->setKey('ses', self::ERROR_SESSION_KEY, null);
        $frontendUser->storeSessionData();

        return self::ERROR_MESSAGES[$errorCode] ?? 'The DocCheck login failed.';
    }

    private function getFrontendUser(): ?FrontendUserAuthentication
    {
        $request = $this->renderingContext->getRequest();

        if (method_exists($request, 'getAttribute')) {
            $frontendUser = $request->getAttribute('frontend.user');
            if ($frontendUser instanceof FrontendUserAuthentication) {
                return $frontendUser;
            }
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
}
