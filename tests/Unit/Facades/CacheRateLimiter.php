<?php

use Illuminate\Cache\RateLimiter;
use WebmanTech\LaravelCache\Facades\CacheRateLimiter;

test('instance', function () {
    expect(CacheRateLimiter::instance())->toBeInstanceOf(RateLimiter::class);
});

test('attempt', function () {
    $maxAttempts = 2;
    $fnCallAttempt = fn() => CacheRateLimiter::attempt('abc-1', $maxAttempts, function () {
        return 'ok';
    });

    for ($i = 0; $i < $maxAttempts; $i++) {
        $executed = $fnCallAttempt();
        expect($executed)->toBe('ok');
    }

    expect($fnCallAttempt())->toBeFalse();
});

test('for', function () {
    CacheRateLimiter::for('test', function () {
        return 'ok';
    });

    $limiter = CacheRateLimiter::limiter('test');
    expect($limiter())->toBe('ok');
});
