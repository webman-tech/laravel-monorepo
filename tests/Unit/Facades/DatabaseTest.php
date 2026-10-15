<?php

use WebmanTech\LaravelDatabase\Facades\DB;
use WebmanTech\LaravelDatabase\Mock\DefaultDatabaseManager;

test('db facade bridges support\Db under webman host', function () {
    $manager = DB::instance();
    expect($manager)->toBeInstanceOf(\Illuminate\Database\DatabaseManager::class);

    DB::statement('create table if not exists facade_db_test (id integer primary key, name text)');
    DB::table('facade_db_test')->insert(['id' => 1, 'name' => 'kriss']);
    $row = DB::table('facade_db_test')->where('id', 1)->first();
    expect($row->name)->toBe('kriss');

    // instance() 带缓存，重复解析返回同一实例
    expect(DB::instance())->toBe($manager);

    // 历史版本兼容 API
    expect(DB::getInstance())->toBe($manager);
});

test('default database manager from config', function () {
    $manager = DefaultDatabaseManager::create();
    expect($manager)->toBeInstanceOf(\Illuminate\Database\DatabaseManager::class);

    // 独立连接，基于 config('database') 的 sqlite memory 配置
    $manager->statement('create table if not exists default_db_manager_test (id integer primary key, name text)');
    $manager->table('default_db_manager_test')->insert(['id' => 1, 'name' => 'kriss']);
    expect($manager->table('default_db_manager_test')->where('id', 1)->first()->name)->toBe('kriss');
});
