<?php

declare(strict_types=1);

namespace Doc2k\DoccheckAccess\ViewHelpers;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class LoginUrlViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('contentElementUid', 'int', 'UID of the DocCheck login content element', true);
    }

    public function render(): string
    {
        $contentElementUid = (int)$this->arguments['contentElementUid'];

        $request = $this->getRequest();
        $language = $request instanceof ServerRequestInterface
            ? $request->getAttribute('language')
            : null;

        $base = '/';

        if ($language instanceof SiteLanguage) {
            $base = (string)$language->getBase();
        }

        $base = rtrim($base, '/') . '/';

        return $base . 'doccheck-access/login/?ce=' . $contentElementUid;
    }

    private function getRequest(): ?ServerRequestInterface
    {
        if (method_exists($this->renderingContext, 'getRequest')) {
            $request = $this->renderingContext->getRequest();

            if ($request instanceof ServerRequestInterface) {
                return $request;
            }
        }

        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;

        return $request instanceof ServerRequestInterface ? $request : null;
    }
}