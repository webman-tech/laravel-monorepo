<?php

return [
    'default' => 'memory',
    'connections' => [
        'memory' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ],
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => runtime_path('database.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],
    ],
];
