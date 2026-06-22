<?php

declare(strict_types=1);

defined('TYPO3') or die();

call_user_func(static function (): void {
    $additionalColumns = [
        'tx_doccheckaccess_button_label' => [
            'exclude' => true,
            'label' => 'Button Label',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'eval' => 'trim',
                'default' => 'Login with DocCheck',
            ],
        ],
        'tx_doccheckaccess_success_pid' => [
            'exclude' => true,
            'label' => 'Success Page',
            'config' => [
                'type' => 'group',
                'allowed' => 'pages',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
                'default' => 0,
            ],
        ],
    ];

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $additionalColumns);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
        [
            'DocCheck Login',
            'doccheckaccess_login',
            'mimetypes-x-content-login',
        ],
        'CType',
        'doccheck_access'
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
        [
            'DocCheck Error Message',
            'doccheckaccess_error_message',
            'mimetypes-x-content-text',
        ],
        'CType',
        'doccheck_access'
    );

    $GLOBALS['TCA']['tt_content']['types']['doccheckaccess_login'] = [
        'showitem' => '
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;general,
                --palette--;;headers,
                tx_doccheckaccess_button_label,
                tx_doccheckaccess_success_pid,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
                --palette--;;frames,
                --palette--;;appearanceLinks,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                --palette--;;language,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                --palette--;;hidden,
                --palette--;;access,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
                categories,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                rowDescription,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
        ',
    ];
    $GLOBALS['TCA']['tt_content']['types']['doccheckaccess_error_message'] = [
        'showitem' => '
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;general,
                --palette--;;headers,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
                --palette--;;frames,
                --palette--;;appearanceLinks,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                --palette--;;language,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                --palette--;;hidden,
                --palette--;;access,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
                categories,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                rowDescription,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
        ',
    ];
    $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['doccheckaccess_login'] = 'mimetypes-x-content-login';
    $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['doccheckaccess_error_message'] = 'mimetypes-x-content-text';
});
