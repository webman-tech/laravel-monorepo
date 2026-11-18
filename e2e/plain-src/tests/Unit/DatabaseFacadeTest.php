<?php

use Illuminate\Contracts\Redis\Factory as RedisFactoryContract;
use WebmanTech\CommonUtils\Container;
use WebmanTech\LaravelDatabase\Facades\DB;
use WebmanTech\LaravelRedis\Facades\Redis;

test('db facade auto-capsule under plain host', function () {
    $manager = DB::getInstance()->getDatabaseManager();
    expect($manager)->toBeInstanceOf(\Illuminate\Database\DatabaseManager::class);

    // 血系锁定：Facade 继承 support\Db（非 webman 宿主经 class_alias 顶替为 Capsule）
    expect(is_subclass_of(DB::class, \support\Db::class))->toBeTrue();

    DB::statement('create table if not exists plain_facade_test (id integer primary key, name text)');
    DB::table('plain_facade_test')->insert(['id' => 1, 'name' => 'kriss']);
    $row = DB::table('plain_facade_test')->where('id', 1)->first();
    expect($row->name)->toBe('kriss');
});

test('default database manager from config', function () {
    // 未绑容器时的兜底：从 config('database') 创建纯 illuminate DatabaseManager
    $manager = \WebmanTech\LaravelDatabase\Mock\DefaultDatabaseManager::create();
    expect($manager)->toBeInstanceOf(\Illuminate\Database\DatabaseManager::class);

    $manager->statement('create table if not exists default_db_manager_test (id integer primary key, name text)');
    $manager->table('default_db_manager_test')->insert(['id' => 1, 'name' => 'kriss']);
    expect($manager->table('default_db_manager_test')->where('id', 1)->first()->name)->toBe('kriss');

    // 装配 events（query 事件派发）与 Eloquent Model resolver（复刻 webman Initializer）
    $sqls = [];
    $manager->listen(function ($query) use (&$sqls) {
        $sqls[] = $query->sql;
    });
    $manager->table('default_db_manager_test')->where('id', 1)->first();
    expect($sqls)->not->toBeEmpty();
    // resolver 兜底注册不抢占：首个装配（上一用例 Facade 触发）已注册，此处仅断言已注册
    expect(\Illuminate\Database\Eloquent\Model::getConnectionResolver())->toBeInstanceOf(\Illuminate\Database\DatabaseManager::class);
});

test('default redis manager without config throws on use', function () {
    // plain 宿主未配置 database.redis：默认 manager 可创建，使用时报明确错误
    $manager = \WebmanTech\LaravelRedis\Mock\DefaultRedisManager::create();
    expect($manager)->toBeInstanceOf(\Illuminate\Redis\RedisManager::class);
    expect(fn () => $manager->connection())->toThrow(\InvalidArgumentException::class);
});

test('redis facade prefers container binding', function () {
    $fakeConnection = new class extends \Illuminate\Redis\Connections\Connection {
        public function createSubscription($channels, \Closure $callback, $method = 'subscribe')
        {
        }
    };
    $fakeFactory = new class ($fakeConnection) implements RedisFactoryContract {
        private $connection;

        public function __construct($connection)
        {
            $this->connection = $connection;
        }

        public function connection($name = null)
        {
            return $this->connection;
        }
    };
    Container::getCurrent()->getRaw()->instance(RedisFactoryContract::class, $fakeFactory);

    expect(Redis::instance())->toBe($fakeFactory);
    expect(Redis::connection('whatever'))->toBe($fakeConnection);
});
