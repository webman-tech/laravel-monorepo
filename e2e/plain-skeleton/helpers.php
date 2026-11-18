<?php

/**
 * 宿主框架全局函数层（非 webman 宿主示例实现）。
 *
 * 非 webman 宿主必须自行提供 webman 约定的全局函数，回退到
 * common-utils 的命名空间实现（有 webman 时全局函数已由 webman-framework 定义，此处全部跳过）。
 */

use function WebmanTech\CommonUtils\app_path as cu_app_path;
use function WebmanTech\CommonUtils\base_path as cu_base_path;
use function WebmanTech\CommonUtils\config as cu_config;
use function WebmanTech\CommonUtils\config_path as cu_config_path;
use function WebmanTech\CommonUtils\get_env as cu_get_env;
use function WebmanTech\CommonUtils\locale as cu_locale;
use function WebmanTech\CommonUtils\logger as cu_logger;
use function WebmanTech\CommonUtils\put_env as cu_put_env;
use function WebmanTech\CommonUtils\runtime_path as cu_runtime_path;
use function WebmanTech\CommonUtils\vendor_path as cu_vendor_path;

if (!function_exists('put_env')) {
    function put_env(string $key, mixed $value): void
    {
        cu_put_env($key, $value);
    }
}

if (!function_exists('get_env')) {
    function get_env(string $key, mixed $default = null, array $whichIsNull = [null, '']): mixed
    {
        return cu_get_env($key, $default, $whichIsNull);
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return cu_base_path($path);
    }
}

if (!function_exists('runtime_path')) {
    function runtime_path(string $path = ''): string
    {
        return cu_runtime_path($path);
    }
}

if (!function_exists('config_path')) {
    function config_path(string $path = ''): string
    {
        return cu_config_path($path);
    }
}

if (!function_exists('app_path')) {
    function app_path(string $path = ''): string
    {
        return cu_app_path($path);
    }
}

if (!function_exists('vendor_path')) {
    function vendor_path(string $path = ''): string
    {
        return cu_vendor_path($path);
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return cu_config($key, $default);
    }
}

if (!function_exists('logger')) {
    function logger(?string $channel = null): \Psr\Log\LoggerInterface
    {
        return cu_logger($channel);
    }
}

if (!function_exists('locale')) {
    function locale(?string $locale = null): string
    {
        return cu_locale($locale);
    }
}
