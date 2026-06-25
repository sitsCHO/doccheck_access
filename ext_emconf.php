<?php

declare(strict_types=1);

$EM_CONF[$_EXTKEY] = [
    'title' => 'DocCheck Access',
    'description' => 'Content element and middleware scaffold for DocCheck access handling.',
    'category' => 'plugin',
    'author' => 'Constantin Horn',
    'author_email' => '',
    'state' => 'beta',
    'version' => '0.4.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-14.9.99',
            'php' => '8.0.0-8.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
