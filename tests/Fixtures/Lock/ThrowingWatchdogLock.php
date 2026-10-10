<?php

namespace Tests\Fixtures\Lock;

use Illuminate\Cache\ArrayStore;
use WebmanTech\LaravelCache\Lock\WatchdogLockTrait;

/**
 * 测试用 stub：renew 抛异常，用于验证定时器回调不会让异常逃逸
 * （逃逸后会被 workerman errorHandler 升级为 stopAll(250) 重启 worker）。
 */
class ThrowingWatchdogLock extends \Illuminate\Cache\ArrayLock
{
    use WatchdogLockTrait;

    public int $renewCalledCount = 0;
    public int $timerAdded = 0;
    public int $timerDeleted = 0;

    /** @var list<callable> */
    private array $pendingCallbacks = [];

    private ?\Closure $logCallback = null;

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

    public function tick(): void
    {
        foreach ($this->pendingCallbacks as $callback) {
            $callback();
        }
    }

    public function currentTimerId(): ?int
    {
        return $this->timerId;
    }

    protected function renew(): bool
    {
        $this->renewCalledCount++;

        throw new \RuntimeException('simulated redis timeout');
    }

    /**
     * 覆写日志写入，用于模拟"日志入口自身抛异常"的降级场景。
     */
    public function onRenewFailureLog(callable $callback): void
    {
        $this->logCallback = $callback;
    }

    protected function logRenewFailure(\Throwable $e): void
    {
        if ($this->logCallback !== null) {
            ($this->logCallback)($e);

            return;
        }

        parent::logRenewFailure($e);
    }
}
