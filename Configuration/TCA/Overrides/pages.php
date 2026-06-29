<?php


defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::registerPageTSConfigFile(
    'doccheck_access',
    'Configuration/PageTsConfig/ContentElements.tsconfig',
    'DocCheck Access'
);