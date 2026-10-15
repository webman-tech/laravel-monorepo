<?php

use WebmanTech\LaravelRedis\Facades\Redis;
use WebmanTech\LaravelRedis\Mock\DefaultRedisManager;

test('redis facade falls back to webman bridge factory', function () {
    $factory = Redis::instance();
    expect($factory)->toBeInstanceOf(\WebmanTech\LaravelRedis\Mock\WebmanRedisFactory::class);
    expect(Redis::instance())->toBe($factory);
});

test('default redis manager from config', function () {
    $manager = DefaultRedisManager::create();
    expect($manager)->toBeInstanceOf(\Illuminate\Redis\RedisManager::class);
});
