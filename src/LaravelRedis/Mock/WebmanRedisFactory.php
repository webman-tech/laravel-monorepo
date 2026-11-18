<?php

namespace WebmanTech\LaravelRedis\Mock;

use Illuminate\Contracts\Redis\Factory;

/**
 * webman 环境下桥接 support\Redis（协程连接池），非 webman 环境给出明确错误
 *
 * @internal
 */
final class WebmanRedisFactory implements Factory
{
    /**
     * @inheritDoc
     */
    public function connection($name = null)
    {
        if (!class_exists('support\Redis')) {
            throw new \RuntimeException(sprintf(
                '%s not exist, install `webman/redis` or bind `%s` into container',
                'support\Redis',
                Factory::class,
            ));
        }
        return \support\Redis::connection($name ?? 'default');
    }
}
