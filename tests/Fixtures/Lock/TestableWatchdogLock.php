<?php

namespace Tests\Fixtures\Lock;

use Illuminate\Cache\ArrayStore;
use WebmanTech\LaravelCache\Lock\WatchdogLockTrait;

/**
 * 测试用 stub：记录 timer 调用并支持手动触发回调
 */
class TestableWatchdogLock extends \Illuminate\Cache\ArrayLock
{
    use WatchdogLockTrait;

    public int $renewCalledCount = 0;
    public bool $renewResult = true;
    public int $timerAdded = 0;
    public int $timerDeleted = 0;

    /** @var list<callable> */
    private array $pendingCallbacks = [];

    public function __construct(ArrayStore $store, string $name, int $seconds, float $renewRatio = 1 / 3)
    {
        parent::__construct($store, $name, $seconds);
        $this->initWatchdog($renewRatio);
    }

    protected function addTimer(int $interval, callable $callback): int
    {
        $this->timerAdded++;
        $this->pendingCallbacks[] = $callback;

        return $this->timerAdded;
    }

    protected function delTimer(int $timerId): void
    {
        $this->timerDeleted++;
    }

    /**
     * 模拟定时器触发：执行所有已注册的回调
     */
    public function tick(): void
    {
        foreach ($this->pendingCallbacks as $callback) {
            $callback();
        }
    }

    protected function renew(): bool
    {
        $this->renewCalledCount++;

        return $this->renewResult;
    }
}
