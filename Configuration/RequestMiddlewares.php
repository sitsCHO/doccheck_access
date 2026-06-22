<?php

declare(strict_types=1);

use Doc2k\DoccheckAccess\Middleware\CallbackMiddleware;
use Doc2k\DoccheckAccess\Middleware\LoginMiddleware;

return [
    'frontend' => [
        'doc2k/doccheck-access/login' => [
            'target' => LoginMiddleware::class,
            'after' => [
                'typo3/cms-frontend/authentication',
            ],
            'before' => [
                'typo3/cms-frontend/page-resolver',
            ],
        ],
        'doc2k/doccheck-access/callback' => [
            'target' => CallbackMiddleware::class,
            'after' => [
                'typo3/cms-frontend/authentication',
            ],
            'before' => [
                'typo3/cms-frontend/page-resolver',
            ],
        ],
    ],
];
