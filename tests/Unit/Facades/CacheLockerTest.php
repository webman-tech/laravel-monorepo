<?php

use Illuminate\Cache\Lock;
use Illuminate\Contracts\Cache\LockTimeoutException;
use WebmanTech\LaravelCache\Facades\CacheLocker;

test('lock', function () {
    // instance
    expect(CacheLocker::test())->toBeInstanceOf(Lock::class)
        ->and(CacheLocker::test('abc'))->toBeInstanceOf(Lock::class);

    // get/release
    $lock = CacheLocker::test();
    expect($lock->get())->toBeTrue()
        ->and($lock->get())->toBeFalse() // 重复获取锁会失败
        ->and($lock->release())->toBeTrue()
        ->and($lock->release())->toBeFalse() // 重复释放锁会失败
        ->and($lock->get())->toBeTrue(); // 再次获取成功

    // 同一个锁释放后不能再用
    // get callback
    $lock = CacheLocker::test('callback_key');
    expect($lock->get(function () {
        // 通过回调函数处理当获取到锁的时候的逻辑
        return 'xxx';
    }))->toEqual('xxx');

    // block
    $lock = CacheLocker::test('block_key', 2);
    expect($lock->get())->toBeTrue();
    $lock2 = CacheLocker::test('block_key', 3);
    expect($lock2->get())->toBeFalse()
        ->and(fn() => $lock2->block(1))->toThrow(LockTimeoutException::class); // 2秒内获取不到锁

    // restore
    $lock = CacheLocker::test('restore_key', 2);
    $owner = $lock->owner();
    expect($owner)->toBeString();
    $lock2 = CacheLocker::restoreTest('restore_key', $owner);
    expect($owner)->toEqual($lock2->owner())
        ->and($lock->get())->toBeTrue()
        ->and($lock2->release())->toBeTrue();
});
