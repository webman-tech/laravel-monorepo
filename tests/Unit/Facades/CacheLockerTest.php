<?php

use Illuminate\Cache\Lock;
use Illuminate\Contracts\Cache\LockTimeoutException;
use WebmanTech\LaravelCache\Facades\CacheLocker;
use WebmanTech\LaravelCache\Lock\WatchdogLock;

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
    $lock->release();

    // get callback
    $lock = CacheLocker::test('callback_key');
    expect($lock->get(function () {
        return 'xxx';
    }))->toEqual('xxx');

    // block
    $lock = CacheLocker::test('block_key', 2);
    expect($lock->get())->toBeTrue();
    $lock2 = CacheLocker::test('block_key', 3);
    expect($lock2->get())->toBeFalse()
        ->and(fn() => $lock2->block(1))->toThrow(LockTimeoutException::class);
    $lock->release();

    // restore
    $lock = CacheLocker::test('restore_key', 2);
    $owner = $lock->owner();
    expect($owner)->toBeString();
    $lock2 = CacheLocker::restoreTest('restore_key', $owner);
    expect($owner)->toEqual($lock2->owner())
        ->and($lock->get())->toBeTrue()
        ->and($lock2->release())->toBeTrue();
});

describe('watchdog lock', function () {
    test('returns WatchdogLock instance', function () {
        $watchdog = CacheLocker::watchdogTest('wd1', 30);
        expect($watchdog)->toBeInstanceOf(WatchdogLock::class);
    });

    test('get with callback', function () {
        $result = CacheLocker::watchdogTest('wd2', 30)->get(fn () => 'done');
        expect($result)->toEqual('done');
    });

    test('acquire and release', function () {
        $watchdog = CacheLocker::watchdogTest('wd3', 30);
        expect($watchdog->acquire())->toBeTrue()
            ->and($watchdog->release())->toBeTrue();

        // 释放后可重新获取
        expect($watchdog->acquire())->toBeTrue();
        $watchdog->release();
    });

    test('forceRelease', function () {
        $watchdog = CacheLocker::watchdogTest('wd4', 30);
        $watchdog->acquire();
        $watchdog->forceRelease();

        // 锁已释放，新的 watchdog 可以获取
        $watchdog2 = CacheLocker::watchdogTest('wd4', 30);
        expect($watchdog2->acquire())->toBeTrue();
        $watchdog2->release();
    });

    test('acquire failure does not block', function () {
        $holder = CacheLocker::watchdogTest('wd5', 30);
        $holder->acquire();

        $competitor = CacheLocker::watchdogTest('wd5', 30);
        expect($competitor->acquire())->toBeFalse();

        $holder->release();
    });

    test('watchdog and normal lock share the same key', function () {
        $normal = CacheLocker::test('wd6', 30);
        $normal->acquire();

        $watchdog = CacheLocker::watchdogTest('wd6', 30);
        expect($watchdog->acquire())->toBeFalse(); // 同名锁，被 normal 持有

        $normal->release();
    });
});
