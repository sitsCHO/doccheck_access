<?php

declare(strict_types=1);

defined('TYPO3') or die();

call_user_func(static function (): void {
    if (method_exists(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::class, 'addPageTSConfig')) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '@import "EXT:doccheck_access/Configuration/TsConfig/Page/ContentElement.tsconfig"'
        );
    }
});
