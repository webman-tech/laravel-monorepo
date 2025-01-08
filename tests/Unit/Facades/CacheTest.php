<?php

use Illuminate\Cache\ApcStore;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;
use WebmanTech\LaravelCache\Exceptions\PreventFlushException;
use WebmanTech\LaravelCache\Facades\Cache;


afterEach(function () {
    Cache::flush();
});

test('instance', function () {
    expect(Cache::instance())->toBeInstanceOf(CacheManager::class);
    expect(Cache::psr16())->toBeInstanceOf(CacheInterface::class);
    expect(Cache::psr6())->toBeInstanceOf(CacheItemPoolInterface::class);
});

test('store', function () {
    expect(Cache::store('apc')->getStore())->toBeInstanceOf(ApcStore::class);
});

test('normal fn', function () {
    // get 获取数据
    expect(Cache::get('not_exist'))->toEqual(null);
    expect(Cache::get('not_exist', 'default'))->toEqual('default');
    expect(Cache::get('not_exist', function () {
        return 'default';
    }))->toEqual('default');

    // add 添加数据，如果存在时不会覆盖
    expect(Cache::add('add_key', 1))->toBeTrue();
    expect(Cache::get('add_key'))->toEqual(1);
    expect(Cache::add('add_key', 1))->toBeFalse();
    expect(Cache::get('add_key'))->toEqual(1);

    // put 添加数据，如果存在时会覆盖
    expect(Cache::put('new_key', 1))->toBeTrue();
    expect(Cache::get('new_key', 222))->toEqual(1);
    expect(Cache::put('new_key', 2))->toBeTrue();
    expect(Cache::get('new_key'))->toEqual(2);

    // has 判断是否存在
    expect(Cache::has('not_exist'))->toBeFalse();
    expect(Cache::has('new_key'))->toBeTrue();

    // forget 删除数据
    Cache::add('forget_key', 1);
    expect(Cache::forget('forget_key'))->toBeTrue();
    expect(Cache::forget('not_exist'))->toBeFalse();

    // put 有效期为0或负数时，也是删除数据
    Cache::add('put_delete_key', 1);
    expect(Cache::put('put_delete_key', 1, 0))->toBeTrue();
    expect(Cache::has('put_delete_key'))->toBeFalse();
    Cache::add('put_delete_key', 1);
    expect(Cache::put('put_delete_key', 1, -5))->toBeTrue();
    expect(Cache::has('put_delete_key'))->toBeFalse();

    // increment 自增
    expect(Cache::increment('increment_key'))->toEqual(1);
    expect(Cache::increment('increment_key'))->toEqual(2);
    Cache::add('increment_key2', 5);
    expect(Cache::increment('increment_key2'))->toEqual(6);
    Cache::add('increment_key3', '6');
    expect(Cache::increment('increment_key3'))->toEqual(7);
    // 递增一个字符串类型的数字
    Cache::add('increment_key4', 'string');
    expect([1, false])->toContain(Cache::increment('increment_key4'));
    // 递增一个字符串，不同的store返回值不一样，可能 false，可能 1
    Cache::add('increment_key5', 1);
    expect(Cache::increment('increment_key5', 9))->toEqual(10);

    // decrement 自减
    expect(Cache::decrement('decrement_key'))->toEqual(-1);
    Cache::add('decrement_key2', 5);
    expect(Cache::decrement('decrement_key2'))->toEqual(4);

    // remember 记住数据，如果已经存在，则直接返回，否则执行 callback 后缓存并返回
    expect(Cache::remember('remember_key', 60, function () {
        return 'remember_value';
    }))->toEqual('remember_value');
    expect(Cache::remember('remember_key', 60, function () {
        return 'remember_value2';
    }))->toEqual('remember_value');
    expect(Cache::get('remember_key'))->toEqual('remember_value');
    expect(Cache::rememberForever('remember_key2', function () {
        return 'remember_value2';
    }))->toEqual('remember_value2');
    expect(Cache::get('remember_key2'))->toEqual('remember_value2');

    // pull 获取数据并删除
    expect(Cache::pull('not_exist'))->toBeNull();
    Cache::add('pull_key', 1);
    expect(Cache::pull('pull_key'))->toEqual(1);
    expect(Cache::has('pull_key'))->toBeFalse();

    // forever 永久缓存
    expect(Cache::forever('forever_key', 1))->toBeTrue();
    expect(Cache::get('forever_key'))->toEqual(1);
});

test('ttl', function () {
    Cache::add('ttl_key', 1, 2);
    expect(Cache::has('ttl_key'))->toBeTrue();
    sleep(2);
    expect(Cache::has('ttl_key'))->toBeFalse();
});

test('tag', function () {
    if (!Cache::supportsTags()) {
        // 对于不支持的不测试
        $this->markTestSkipped('This store not support tags');
    }

    // 将数据放到某 tag 下
    Cache::tags('tag1')->put('tag_key', 1);
    expect(Cache::tags('tag1')->has('tag_key'))->toBeTrue();

    // 清空 tag，不清其他数据
    Cache::add('global_key', 1);
    Cache::tags('tag1')->add('tag_key', 1);
    Cache::tags('tag1')->flush();
    expect(Cache::has('global_key'))->toBeTrue();
    expect(Cache::tags('tag1')->has('tag_key'))->toBeFalse();

    // 多tag逻辑
    // 数据
    Cache::tags(['tag1', 'tag2'])->add('tag_key', 1);
    Cache::tags(['tag1', 'tag3'])->add('tag_key2', 1);

    // 必须通过两个 tag 才能查到，单个 tag 查不到数据
    expect(Cache::tags(['tag1', 'tag2'])->has('tag_key'))->toBeTrue();
    expect(Cache::tags('tag1')->has('tag_key'))->toBeFalse();

    // 清空 tag1, tag3，将清空 tag_key
    Cache::tags(['tag1', 'tag3'])->flush();
    expect(Cache::tags(['tag1', 'tag2'])->has('tag_key'))->toBeFalse();

    // 数据
    Cache::tags(['tag1', 'tag2'])->add('tag_key', 1);
    Cache::tags(['tag1', 'tag3'])->add('tag_key2', 1);

    // 清空 tag3，将保留 tag_key
    Cache::tags('tag3')->flush();
    expect(Cache::tags(['tag1', 'tag2'])->has('tag_key'))->toBeTrue();
});

test('lock', function () {
    expect(Cache::lock('lock_key'))->toBeInstanceOf(Lock::class);

    // get release
    $lock = Cache::lock('lock_key');
    expect($lock->get())->toBeTrue();
    expect($lock->get())->toBeFalse();
    // 重复获取锁会失败
    expect($lock->release())->toBeTrue();
    expect($lock->release())->toBeFalse();
    // 重复释放锁会失败
    expect($lock->get())->toBeTrue();

    // 同一个锁释放后不能再用
    // get callback
    $lock = Cache::lock('lock_key2');
    expect($lock->get(function () {
        // 通过回调函数处理当获取到锁的时候的逻辑
        return 'xxx';
    }))->toEqual('xxx');

    // block
    $lock = Cache::lock('lock_key3', 2);
    expect($lock->get())->toBeTrue();
    $lock2 = Cache::lock('lock_key3', 3);
    expect($lock2->get())->toBeFalse();
    // 2秒内获取不到锁
    expect(fn() => $lock2->block(1))->toThrow(LockTimeoutException::class);

    // restore
    $lock = Cache::lock('lock_key4', 2);
    $owner = $lock->owner();
    expect($owner)->toBeString();
    $lock2 = Cache::restoreLock('lock_key4', $owner);
    expect($owner)->toEqual($lock2->owner());
    expect($lock->get())->toBeTrue();
    expect($lock2->release())->toBeTrue();
});

test('flush prevent', function () {
    // ignore 掉的 store 可以正常 flush
    expect(Cache::store('null')->flush())->toBeTrue();

    // 非 ignore 的不能 flush
    try {
        Cache::store('array')->flush();
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(PreventFlushException::class);
    }

    // 直接取到 store 仍然可以 flush
    expect(Cache::store('array')->getStore()->flush())->toBeTrue();
});
