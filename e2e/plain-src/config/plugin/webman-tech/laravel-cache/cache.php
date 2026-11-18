<?php

return [
    'default' => 'array',
    'stores' => [
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],
        'file' => [
            'driver' => 'file',
            'path' => runtime_path('cache'),
        ],
    ],
    'prefix' => '',
    'extend' => null,
];
