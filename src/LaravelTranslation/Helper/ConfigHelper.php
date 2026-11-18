<?php

namespace WebmanTech\LaravelTranslation\Helper;

use function WebmanTech\CommonUtils\config;

/**
 * @internal
 */
final class ConfigHelper
{
    /**
     * 获取配置
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return config("plugin.webman-tech.laravel-translation.{$key}", $default);
    }

    public static function getGlobal(string $key, mixed $default = null): mixed
    {
        return config($key, $default);
    }
}
