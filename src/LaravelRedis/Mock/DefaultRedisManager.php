<?php

namespace WebmanTech\LaravelRedis\Mock;

use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Redis\RedisManager;
use function WebmanTech\CommonUtils\config;

/**
 * 纯 illuminate 的默认 RedisManager，从 config('database.redis') 创建
 *
 * 与 webman 下 support\Redis 的行为对齐（装包 + 配置即可用）；连接配置缺失时在使用时报错
 *
 * @internal
 */
final class DefaultRedisManager
{
    public static function create(): RedisManager
    {
        $config = (array)config('database.redis', []);

        // RedisManager 仅在绑定 events 时使用，未绑定自动跳过
        // 实际仅需容器的 bound() / make() 能力，无需完整 Application
        /** @phpstan-ignore-next-line */
        return new RedisManager(new IlluminateContainer(), $config['client'] ?? 'phpredis', $config);
    }
}
