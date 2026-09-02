<?php

use support\Log;
use support\Request;

return [
    'webman' => [
        'handler' => \Webman\App::class,
        'listen' => 'http://0.0.0.0:' . (getenv('APP_PORT') ?: 8787),
        'count' => 1,
        'user' => '',
        'group' => '',
        'reusePort' => false,
        'eventLoop' => '',
        'context' => [],
        'constructor' => [
            'requestClass' => Request::class,
            'logger' => Log::channel('default'),
            'appPath' => base_path() . '/app',
            'publicPath' => base_path() . '/public',
        ],
    ],
];
