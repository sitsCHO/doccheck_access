<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'DocCheck Access',
    'description' => 'Content element and middleware scaffold for DocCheck access handling.',
    'category' => 'plugin',
    'author' => 'Constantin Horn',
    'author_email' => 't3extensions@doc2k.de',
    'state' => 'stable',
    'version' => '1.0.3',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-14.9.99',
            'php' => '8.0.0-8.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
