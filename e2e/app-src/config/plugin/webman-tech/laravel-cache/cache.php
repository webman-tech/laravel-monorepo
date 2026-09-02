<?php

use Illuminate\Support\Str;

return [
    'default' => getenv('CACHE_DRIVER') ?: 'file',
    'stores' => [
        'apc' => ['driver' => 'apc'],
        'array' => ['driver' => 'array', 'serialize' => false],
        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
            'lock_connection' => null,
        ],
        'file' => [
            'driver' => 'file',
            'path' => runtime_path('cache'),
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
        ],
    ],
    'prefix' => '',
    'extend' => null,
];
