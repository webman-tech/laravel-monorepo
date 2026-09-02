<?php

return [
    'log' => [
        'enable' => true,
        'channel' => 'default',
        'level' => 'info',
        'format' => \GuzzleHttp\MessageFormatter::CLF,
    ],
    'guzzle' => [
        'debug' => false,
        'timeout' => 10,
    ],
    'macros' => [],
];
