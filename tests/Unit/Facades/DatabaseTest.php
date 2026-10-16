<?php

use WebmanTech\LaravelDatabase\Facades\DB;
use WebmanTech\LaravelDatabase\Mock\DefaultDatabaseManager;

test('db facade bridges support\Db under webman host', function () {
    $manager = DB::getInstance()->getDatabaseManager();
    expect($manager)->toBeInstanceOf(\Illuminate\Database\DatabaseManager::class);

    DB::statement('create table if not exists facade_db_test (id integer primary key, name text)');
    DB::table('facade_db_test')->insert(['id' => 1, 'name' => 'kriss']);
    $row = DB::table('facade_db_test')->where('id', 1)->first();
    expect($row->name)->toBe('kriss');

    // 真相源单例：重复获取同一实例（静态调用与 getInstance() 同源）
    expect(DB::getInstance()->getDatabaseManager())->toBe($manager);

    // 血系锁定：Facade 继承 support\Db（与 v12.0.14 行为一致）
    expect(is_subclass_of(DB::class, \support\Db::class))->toBeTrue();

    // 历史 API：getInstance() 返回 Capsule 全局实例（webman 下即初始装配的实例）
    expect(DB::getInstance())->toBeInstanceOf(\Illuminate\Database\Capsule\Manager::class);
});

test('default database manager from config', function () {
    $manager = DefaultDatabaseManager::create();
    expect($manager)->toBeInstanceOf(\Illuminate\Database\DatabaseManager::class);

    // 独立连接，基于 config('database') 的 sqlite memory 配置
    $manager->statement('create table if not exists default_db_manager_test (id integer primary key, name text)');
    $manager->table('default_db_manager_test')->insert(['id' => 1, 'name' => 'kriss']);
    expect($manager->table('default_db_manager_test')->where('id', 1)->first()->name)->toBe('kriss');

    // 装配 events（DatabaseManager 默认 events 为 null 时 query 事件静默跳过）
    $sqls = [];
    $manager->listen(function ($query) use (&$sqls) {
        $sqls[] = $query->sql;
    });
    $manager->table('default_db_manager_test')->where('id', 1)->first();
    expect($sqls)->not->toBeEmpty();

    // 装配 Eloquent：Model 的 connection resolver 已注册（复刻 webman Initializer 的 bootEloquent；
    // 主套件下 webman 桥接与 default 双体系共存，resolver 兜底不抢占，此处仅断言已注册）
    expect(\Illuminate\Database\Eloquent\Model::getConnectionResolver())->not->toBeNull();
});
