<?php


defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile(
    'doccheck_access',
    'Configuration/TypoScript',
    'DocCheck Access'
);