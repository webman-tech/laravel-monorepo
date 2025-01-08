<?php

if (base_path('/config/app.php')) {
    copy_dir(__DIR__. '/config', base_path('/config'));
}

if (!is_dir(base_path('resource/translations'))) {
    copy_dir(__DIR__. '/resource', base_path('/resource'));
}

if (!file_exists(storage_path('app'))) {
    \WebmanTech\LaravelFilesystem\Install::install();
}

require_once __DIR__ . '/../vendor/workerman/webman-framework/src/support/bootstrap.php';
