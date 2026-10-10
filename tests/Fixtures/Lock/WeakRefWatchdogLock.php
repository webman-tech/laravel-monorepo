<?php

namespace Tests\Fixtures\Lock;

use Illuminate\Cache\ArrayStore;
use WebmanTech\LaravelCache\Lock\WatchdogLockTrait;

/**
 * 测试用 stub：用于验证定时器回调不会强引用锁对象。
 *
 * 回调存入外部 $loop（模拟事件循环的 timer 注册表），本类不持有，
 * 以此复现真实引用关系并检验锁对象能否被 GC、__destruct 能否触发。
 * 计数写入外部 $counter，避免锁对象被闭包间接强引用。
 */
class WeakRefWatchdogLock extends \Illuminate\Cache\ArrayLock
{
    use WatchdogLockTrait;

    public int $timerAdded = 0;

    public function __construct(
        ArrayStore $store,
        string $name,
        int $seconds,
        public \stdClass $counter,
        public \stdClass $loop,
        float $renewRatio = 1 / 3
    ) {
        parent::__construct($store, $name, $seconds);
        $this->initWatchdog($renewRatio);
    }

    public function toWeakRef(): \WeakReference
    {
        return $this->weakRef ??= \WeakReference::create($this);
    }

    private ?\WeakReference $weakRef = null;

    protected function addTimer(int $interval, callable $callback): int
    {
        $this->timerAdded++;
        $this->loop->callbacks[] = $callback;

        return $this->timerAdded;
    }

    protected function delTimer(int $timerId): void
    {
        $this->counter->del++;
    }

    protected function renew(): bool
    {
        $this->counter->renew++;

        return true;
    }
}
