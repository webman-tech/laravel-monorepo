<?php

namespace WebmanTech\LaravelDatabase\Mock;

use Illuminate\Config\Repository;
use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager;
use function WebmanTech\CommonUtils\config;

/**
 * 纯 illuminate 的默认 DatabaseManager，从 config('database') 创建
 *
 * 与 webman 下 support\Db 的行为对齐（装包 + 配置即可用）；连接配置缺失时在使用时报错
 *
 * @internal
 */
final class DefaultDatabaseManager
{
    public static function create(): DatabaseManager
    {
        // DatabaseManager 仅在绑定 events / db.transactions 时使用，未绑定自动跳过
        $app = new IlluminateContainer();
        $app->instance('config', new Repository([
            'database' => (array)config('database', []),
        ]));

        // 实际仅需容器的 ArrayAccess / bound() 能力，无需完整 Application
        /** @phpstan-ignore-next-line */
        return new DatabaseManager($app, new ConnectionFactory($app));
    }
}
