<?php

namespace WebmanTech\LaravelHttp\Helper;

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
        return config("plugin.webman-tech.laravel-http.{$key}", $default);
    }
}
