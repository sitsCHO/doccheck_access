<?php

declare(strict_types=1);

namespace Doc2k\DoccheckAccess\ViewHelpers;

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

        $request = $this->renderingContext->getRequest();
        $language = $request->getAttribute('language');

        $base = '/';

        if ($language instanceof SiteLanguage) {
            $base = (string)$language->getBase();
        }

        $base = rtrim($base, '/') . '/';

        return $base . 'doccheck-access/login/?ce=' . $contentElementUid;
    }
}