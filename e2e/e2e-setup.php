<?php

declare(strict_types=1);

use WebmanTech\Testing\E2eSetup\Definition\AppConfig;
use WebmanTech\Testing\E2eSetup\SetupConfig;

return SetupConfig::configure()
    ->app('webman', AppConfig::configure()
        ->skeleton('workerman/webman')
        ->targetDir('webman')
        ->srcDir('app-src')
        ->package('webman-tech/laravel-monorepo')
        ->require([
            'overtrue/flysystem-cos' => '^5.1',
            'overtrue/flysystem-qiniu' => '^3.2',
            'alphasnow/aliyun-oss-laravel' => '^4.9',
            'webman/console' => '^2.1',
        ])
        ->requireDev([
            'pestphp/pest' => '^3.7',
            'guzzlehttp/guzzle' => '^7.8',
            'webman-tech/testing' => '^1.1',
            'webman-tech/laravel-monorepo' => 'dev-main',
            'symfony/cache' => '^7.0',
            'symfony/mime' => '^7.0',
            'symfony/translation' => '^7.0',
        ])
        ->requireOverride([
            'monolog/monolog' => '^3.8',
        ])
        ->reinstallPackages([
            'webman/console',
            'webman/database',
            'webman/redis',
            'webman-tech/laravel-cache',
            'webman-tech/laravel-console',
            'webman-tech/laravel-database',
            'webman-tech/laravel-filesystem',
            'webman-tech/laravel-http',
            'webman-tech/laravel-process',
            'webman-tech/laravel-redis',
            'webman-tech/laravel-translation',
            'webman-tech/laravel-validation',
        ])
    );
