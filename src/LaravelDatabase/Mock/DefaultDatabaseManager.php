<?php

namespace WebmanTech\LaravelDatabase\Mock;

use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Database\Capsule\Manager as CapsuleManager;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use function WebmanTech\CommonUtils\config;

/**
 * 纯 illuminate 的默认数据库装配器，从 config('database') 创建
 *
 * 装配方式与 webman/database Initializer::init 一致（addConnection + setEventDispatcher + bootEloquent）；
 * 连接配置缺失时在使用时报错
 *
 * @internal
 */
final class DefaultDatabaseManager
{
    public static function create(): DatabaseManager
    {
        return self::createCapsule()->getDatabaseManager();
    }

    /**
     * 装配 Capsule 实例（不 setAsGlobal：全局静态由 Facade\DB::getInstance() 兜底回填）
     */
    public static function createCapsule(): CapsuleManager
    {
        /** @var array<string, mixed> $dbConfig */
        $dbConfig = (array)config('database', []);
        /** @var array<string, array> $connections */
        $connections = (array)($dbConfig['connections'] ?? []);
        /** @var string|false $default */
        $default = $dbConfig['default'] ?? false;

        $capsule = new CapsuleManager(new IlluminateContainer());
        if ($default && isset($connections[$default])) {
            $capsule->addConnection($connections[$default], $default);
            $capsule->getDatabaseManager()->setDefaultConnection($default);
            unset($connections[$default]);
        }
        foreach ($connections as $name => $item) {
            $capsule->addConnection($item, $name);
        }

        // query/事务事件派发（DatabaseManager 默认 events 为 null 时会静默跳过）
        $capsule->setEventDispatcher(new Dispatcher());

        // 注册 Eloquent Model 的 connection resolver / event dispatcher，宿主可直接使用 Model
        // 仅兜底：已有装配（webman 桥接 / 宿主自定义）时不覆盖，避免全局 resolver 被默认实现抢占
        if (Model::getConnectionResolver() === null) {
            $capsule->bootEloquent();
        }

        return $capsule;
    }
}
