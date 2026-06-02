<?php

use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\RedisLock;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Carbon;
use Symfony\Component\Clock\MockClock;
use Tests\Fixtures\Lock\TestableWatchdogLock;
use WebmanTech\CommonUtils\Timer\PcntlTimer;
use WebmanTech\LaravelCache\Lock\WatchdogArrayLock;
use WebmanTech\LaravelCache\Lock\WatchdogCacheLock;
use WebmanTech\LaravelCache\Lock\WatchdogDatabaseLock;
use WebmanTech\LaravelCache\Lock\WatchdogLock;
use WebmanTech\LaravelCache\Lock\WatchdogMemcachedLock;
use WebmanTech\LaravelCache\Lock\WatchdogRedisLock;

beforeEach(function () {
    $this->mockClock = new MockClock('2025-01-01 00:00:00');
    Carbon::setTestNow($this->mockClock->now());
    PcntlTimer::setClock($this->mockClock);
});

afterEach(function () {
    PcntlTimer::delAll();
    PcntlTimer::setClock(null);
    Carbon::setTestNow();
});

describe('WatchdogLock 构造与分发', function () {
    test('create accepts AbstractLock', function () {
        $store = new ArrayStore();
        $watchdog = WatchdogLock::create($store->lock('test', 30));
        expect($watchdog)->toBeInstanceOf(WatchdogLock::class);
    });

    test('create rejects non-AbstractLock', function () {
        $mock = new class implements Lock {
            public function get($callback = null) { return true; }
            public function block($seconds, $callback = null) { return true; }
            public function release() { return true; }
            public function owner() { return 'test'; }
            public function forceRelease() {}
        };
        WatchdogLock::create($mock);
    })->throws(InvalidArgumentException::class);

    test('creates WatchdogArrayLock for ArrayStore', function () {
        $store = new ArrayStore();
        $watchdog = new WatchdogLock($store->lock('test', 30));

        $inner = (new ReflectionProperty($watchdog, 'inner'))->getValue($watchdog);
        expect($inner)->toBeInstanceOf(WatchdogArrayLock::class);
    });

    test('delegates owner', function () {
        $store = new ArrayStore();
        $lock = $store->lock('test', 30);
        $watchdog = new WatchdogLock($lock);

        expect($watchdog->owner())->toEqual($lock->owner());
    });

    test('delegates block with callback', function () {
        $store = new ArrayStore();
        $watchdog = new WatchdogLock($store->lock('test', 30));

        $result = $watchdog->block(1, fn () => 'blocked');
        expect($result)->toEqual('blocked');
    });

    test('delegates block without callback acquires lock', function () {
        $store = new ArrayStore();
        $watchdog = new WatchdogLock($store->lock('test', 30));

        expect($watchdog->block(1))->toBeTrue();
        $watchdog->release();
    });
});

describe('initWatchdog 续期间隔计算', function () {
    test('calculates renewInterval with default ratio', function () {
        $store = new ArrayStore();
        $lock = new TestableWatchdogLock($store, 't', 30);

        $interval = (new ReflectionProperty($lock, 'renewInterval'))->getValue($lock);
        expect($interval)->toEqual(10); // 30 * 1/3 = 10
    });

    test('ensures minimum interval of 1 second', function () {
        $store = new ArrayStore();
        $lock = new TestableWatchdogLock($store, 't', 1);

        $interval = (new ReflectionProperty($lock, 'renewInterval'))->getValue($lock);
        expect($interval)->toEqual(1); // max(1, 0) = 1
    });

    test('respects custom renewRatio', function () {
        $store = new ArrayStore();
        $lock = new TestableWatchdogLock($store, 't', 30, 0.5);

        $interval = (new ReflectionProperty($lock, 'renewInterval'))->getValue($lock);
        expect($interval)->toEqual(15); // 30 * 0.5 = 15
    });
});

describe('WatchdogLockTrait 行为', function () {
    test('acquire starts watchdog timer', function () {
        $store = new ArrayStore();
        $lock = new TestableWatchdogLock($store, 't1', 30);

        expect($lock->acquire())->toBeTrue()
            ->and($lock->timerAdded)->toEqual(1);
    });

    test('release stops watchdog timer', function () {
        $store = new ArrayStore();
        $lock = new TestableWatchdogLock($store, 't2', 30);
        $lock->acquire();

        expect($lock->release())->toBeTrue()
            ->and($lock->timerDeleted)->toEqual(1);
    });

    test('forceRelease stops watchdog timer', function () {
        $store = new ArrayStore();
        $lock = new TestableWatchdogLock($store, 't3', 30);
        $lock->acquire();
        $lock->forceRelease();

        expect($lock->timerDeleted)->toEqual(1);
    });

    test('acquire failure does not start watchdog', function () {
        $store = new ArrayStore();
        $holder = $store->lock('t4', 30);
        $holder->acquire();

        $lock = new TestableWatchdogLock($store, 't4', 30);
        expect($lock->acquire())->toBeFalse()
            ->and($lock->timerAdded)->toEqual(0);
    });

    test('get with callback starts and stops watchdog', function () {
        $store = new ArrayStore();
        $lock = new TestableWatchdogLock($store, 't5', 30);

        $result = $lock->get(fn () => 'ok');

        expect($result)->toEqual('ok')
            ->and($lock->timerAdded)->toEqual(1)
            ->and($lock->timerDeleted)->toEqual(1);
    });

    test('get without callback starts watchdog, manual release stops it', function () {
        $store = new ArrayStore();
        $lock = new TestableWatchdogLock($store, 't6', 30);

        expect($lock->get())->toBeTrue()
            ->and($lock->timerAdded)->toEqual(1);

        expect($lock->release())->toBeTrue()
            ->and($lock->timerDeleted)->toEqual(1);
    });

    test('lock released after get callback is reusable', function () {
        $store = new ArrayStore();
        $lock1 = new TestableWatchdogLock($store, 't7', 30);

        $lock1->get(fn () => 'done');

        $lock2 = new TestableWatchdogLock($store, 't7', 30);
        expect($lock2->acquire())->toBeTrue()
            ->and($lock2->timerAdded)->toEqual(1);
    });
});

describe('Watchdog timer 回调行为', function () {
    test('tick calls renew', function () {
        $store = new ArrayStore();
        $lock = new TestableWatchdogLock($store, 'tick1', 30);
        $lock->acquire();

        expect($lock->renewCalledCount)->toEqual(0);

        $lock->tick();

        expect($lock->renewCalledCount)->toEqual(1);
    });

    test('stops when renew fails', function () {
        $store = new ArrayStore();
        $lock = new TestableWatchdogLock($store, 'tick2', 30);
        $lock->acquire();

        $lock->renewResult = false;

        $lock->tick();

        expect($lock->renewCalledCount)->toEqual(1)
            ->and($lock->timerDeleted)->toEqual(1);
    });

    test('continues when renew succeeds', function () {
        $store = new ArrayStore();
        $lock = new TestableWatchdogLock($store, 'tick3', 30);
        $lock->acquire();

        $lock->tick();
        $lock->tick();

        expect($lock->renewCalledCount)->toEqual(2)
            ->and($lock->timerDeleted)->toEqual(0);
    });
});

describe('ArrayLock 真实续期行为', function () {
    test('renew extends expiresAt', function () {
        $store = new ArrayStore();
        $lock = new WatchdogArrayLock($store, 'test', 10);
        $lock->acquire();

        $initialExpires = $store->locks['test']['expiresAt'];
        expect($initialExpires)->not->toBeNull();

        // renewInterval = max(1, 10 * 1/3) = 3，推进 4s 确保触发
        $this->mockClock->sleep(4);
        Carbon::setTestNow($this->mockClock->now());
        PcntlTimer::tick();

        $renewedExpires = $store->locks['test']['expiresAt'];
        expect($renewedExpires->getTimestamp())->toBeGreaterThan($initialExpires->getTimestamp());

        $lock->release();
    });

    test('survives beyond original TTL via repeated renew', function () {
        $store = new ArrayStore();
        $lock = new WatchdogArrayLock($store, 'test', 10);
        $lock->acquire();

        for ($i = 0; $i < 5; $i++) {
            $this->mockClock->sleep(4);
            Carbon::setTestNow($this->mockClock->now());
            PcntlTimer::tick();
        }

        // 累计推进 20s，远超原始 10s TTL，锁仍存在
        expect($store->locks)->toHaveKey('test');

        $lock->release();
        expect($store->locks)->not->toHaveKey('test');
    });

    test('without watchdog expires after TTL', function () {
        $store = new ArrayStore();
        $lock = $store->lock('test', 5);
        $lock->acquire();

        $this->mockClock->sleep(6);
        Carbon::setTestNow($this->mockClock->now());

        $anotherLock = $store->lock('test', 5);
        expect($anotherLock->acquire())->toBeTrue();
    });

    test('with watchdog prevents another acquire during renewal', function () {
        $store = new ArrayStore();
        $lock = new WatchdogArrayLock($store, 'test', 10);
        $lock->acquire();

        $this->mockClock->sleep(4);
        Carbon::setTestNow($this->mockClock->now());
        PcntlTimer::tick();

        $this->mockClock->sleep(4);
        Carbon::setTestNow($this->mockClock->now());
        PcntlTimer::tick();

        $competitor = $store->lock('test', 10);
        expect($competitor->acquire())->toBeFalse();

        $lock->release();
    });

    test('watchdog stops when lock is force released externally', function () {
        $store = new ArrayStore();
        $lock = new WatchdogArrayLock($store, 'test', 10);
        $lock->acquire();

        $store->lock('test', 10)->forceRelease();

        $this->mockClock->sleep(4);
        Carbon::setTestNow($this->mockClock->now());
        PcntlTimer::tick();

        expect($store->locks)->not->toHaveKey('test');
    });

    test('renew with seconds=0 sets expiresAt to null', function () {
        $store = new ArrayStore();
        $lock = new WatchdogArrayLock($store, 'test', 0);
        $lock->acquire();

        expect($store->locks['test']['expiresAt'])->toBeNull();

        $this->mockClock->sleep(2);
        Carbon::setTestNow($this->mockClock->now());
        PcntlTimer::tick();

        // renew 后仍为 null（seconds=0 不设过期）
        expect($store->locks['test']['expiresAt'])->toBeNull();

        $lock->release();
    });
});

describe('WatchdogLock 完整流程', function () {
    test('full lifecycle with callback', function () {
        $store = new ArrayStore();
        $watchdog = new WatchdogLock($store->lock('test', 10));

        $result = $watchdog->get(function () {
            $this->mockClock->sleep(4);
            Carbon::setTestNow($this->mockClock->now());
            PcntlTimer::tick();

            $this->mockClock->sleep(4);
            Carbon::setTestNow($this->mockClock->now());
            PcntlTimer::tick();

            return 'task-result';
        });

        expect($result)->toEqual('task-result');

        $another = $store->lock('test', 10);
        expect($another->acquire())->toBeTrue();
    });
});

describe('WatchdogCacheLock renew', function () {
    test('renew returns false when not owned', function () {
        $store = new ArrayStore();
        $lock = new WatchdogCacheLock($store, 'cache1', 10);
        // 不 acquire，直接调用 renew

        $renew = new ReflectionMethod($lock, 'renew');
        expect($renew->invoke($lock))->toBeFalse();
    });

    test('renew with seconds > 0 calls store put', function () {
        $store = new ArrayStore();
        $lock = new WatchdogCacheLock($store, 'cache2', 10);
        $lock->acquire();

        $renew = new ReflectionMethod($lock, 'renew');
        expect($renew->invoke($lock))->toBeTrue();
    });

    test('renew with seconds = 0 returns true', function () {
        $store = new ArrayStore();
        $lock = new WatchdogCacheLock($store, 'cache3', 0);
        $lock->acquire();

        $renew = new ReflectionMethod($lock, 'renew');
        expect($renew->invoke($lock))->toBeTrue();
    });
});

describe('WatchdogRedisLock renew', function () {
    test('fromLock creates instance', function () {
        $redis = new class {
            public function eval(string $script, int $numKeys, string ...$args): int
            {
                return 1;
            }
        };
        $redisLock = new RedisLock($redis, 'redis1', 10, 'test-owner');
        $watchdog = WatchdogRedisLock::fromLock($redisLock);

        expect($watchdog)->toBeInstanceOf(WatchdogRedisLock::class);
    });

    test('renew calls redis eval with Lua script', function () {
        $evaluated = false;
        $redis = new class($evaluated) {
            public static bool $wasCalled = false;

            public function eval(string $script, int $numKeys, string ...$args): int
            {
                self::$wasCalled = true;

                return 1;
            }
        };
        $lock = new WatchdogRedisLock($redis, 'redis2', 10, 'test-owner');

        $renew = new ReflectionMethod($lock, 'renew');
        expect($renew->invoke($lock))->toBeTrue();
        expect($redis::$wasCalled)->toBeTrue();
    });

    test('renew returns false when redis eval returns 0', function () {
        $redis = new class {
            public function eval(string $script, int $numKeys, string ...$args): int
            {
                return 0;
            }
        };
        $lock = new WatchdogRedisLock($redis, 'redis3', 10, 'test-owner');

        $renew = new ReflectionMethod($lock, 'renew');
        expect($renew->invoke($lock))->toBeFalse();
    });
});

describe('WatchdogDatabaseLock renew', function () {
    test('renew returns true when update affects 1 row', function () {
        $queryBuilder = new class {
            public function table(string $table): self { return $this; }
            public function where(string $col, string $val): self { return $this; }
            public function update(array $data): int { return 1; }
        };

        $lock = (new ReflectionClass(WatchdogDatabaseLock::class))
            ->newInstanceWithoutConstructor();
        (function () use ($queryBuilder) {
            $this->connection = $queryBuilder;
            $this->table = 'locks';
            $this->name = 'db1';
            $this->owner = 'test-owner';
            $this->seconds = 10;
        })->bindTo($lock, WatchdogDatabaseLock::class)();

        $renew = new ReflectionMethod($lock, 'renew');
        expect($renew->invoke($lock))->toBeTrue();
    });

    test('renew returns false when update affects 0 rows', function () {
        $queryBuilder = new class {
            public function table(string $table): self { return $this; }
            public function where(string $col, string $val): self { return $this; }
            public function update(array $data): int { return 0; }
        };

        $lock = (new ReflectionClass(WatchdogDatabaseLock::class))
            ->newInstanceWithoutConstructor();
        (function () use ($queryBuilder) {
            $this->connection = $queryBuilder;
            $this->table = 'locks';
            $this->name = 'db2';
            $this->owner = 'test-owner';
            $this->seconds = 10;
        })->bindTo($lock, WatchdogDatabaseLock::class)();

        $renew = new ReflectionMethod($lock, 'renew');
        expect($renew->invoke($lock))->toBeFalse();
    });
});

describe('WatchdogMemcachedLock renew', function () {
    test('renew returns false when not owned', function () {
        $memcached = new class {
            public function get(string $key): mixed { return false; }
            public function add(string $key, mixed $value, int $seconds): bool { return true; }
            public function touch(string $key, int $seconds): bool { return true; }
        };

        $lock = (new ReflectionClass(WatchdogMemcachedLock::class))
            ->newInstanceWithoutConstructor();
        (function () use ($memcached) {
            $this->memcached = $memcached;
            $this->name = 'mc1';
            $this->owner = 'other-owner';
            $this->seconds = 10;
        })->bindTo($lock, WatchdogMemcachedLock::class)();

        $renew = new ReflectionMethod($lock, 'renew');
        expect($renew->invoke($lock))->toBeFalse();
    });

    test('renew returns true when touch succeeds', function () {
        $memcached = new class {
            public function get(string $key): mixed { return 'test-owner'; }
            public function add(string $key, mixed $value, int $seconds): bool { return true; }
            public function touch(string $key, int $seconds): bool { return true; }
        };

        $lock = (new ReflectionClass(WatchdogMemcachedLock::class))
            ->newInstanceWithoutConstructor();
        (function () use ($memcached) {
            $this->memcached = $memcached;
            $this->name = 'mc2';
            $this->owner = 'test-owner';
            $this->seconds = 10;
        })->bindTo($lock, WatchdogMemcachedLock::class)();

        $renew = new ReflectionMethod($lock, 'renew');
        expect($renew->invoke($lock))->toBeTrue();
    });

    test('renew returns false when touch fails', function () {
        $memcached = new class {
            public function get(string $key): mixed { return 'test-owner'; }
            public function add(string $key, mixed $value, int $seconds): bool { return true; }
            public function touch(string $key, int $seconds): bool { return false; }
        };

        $lock = (new ReflectionClass(WatchdogMemcachedLock::class))
            ->newInstanceWithoutConstructor();
        (function () use ($memcached) {
            $this->memcached = $memcached;
            $this->name = 'mc3';
            $this->owner = 'test-owner';
            $this->seconds = 10;
        })->bindTo($lock, WatchdogMemcachedLock::class)();

        $renew = new ReflectionMethod($lock, 'renew');
        expect($renew->invoke($lock))->toBeFalse();
    });
});
