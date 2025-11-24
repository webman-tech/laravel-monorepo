<?php

use WebmanTech\CommonUtils\Constants;
use WebmanTech\CommonUtils\Runtime;

Runtime::$RUNTIME = Constants::RUNTIME_WEBMAN;

function get_env(string $key, $default = null)
{
    return \WebmanTech\CommonUtils\get_env($key, $default);
}

if (file_exists(__DIR__ . '/env.php')) {
    require_once __DIR__ . '/env.php';
}

copy_dir(__DIR__ . '/webman', base_path());

if (!file_exists(storage_path('app'))) {
    \WebmanTech\LaravelFilesystem\Install::install();
}

require_once __DIR__ . '/../vendor/workerman/webman-framework/src/support/bootstrap.php';
