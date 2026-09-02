<?php

declare(strict_types=1);

// 加载 composer autoload
require_once dirname(__DIR__) . '/vendor/autoload.php';

// 同步 $_ENV 到 $_SERVER 和 putenv（phpunit <env> 注入的变量需要在此处可见）
// getenv() 读取的是 putenv() 设置的环境变量，PHPUnit <env> 不一定调用 putenv()
foreach ($_ENV as $name => $value) {
    $_SERVER[$name] = $value;
    putenv("$name=$value");
}

// 引导 webman 环境（使 storage_path/base_path/config_path 等函数可用）
$appRoot = dirname(__DIR__);
if (file_exists($appRoot . '/support/bootstrap.php')) {
    require_once $appRoot . '/support/bootstrap.php';
}
