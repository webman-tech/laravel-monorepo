<?php

use Webman\Http\Response as WebmanResponse;
use WebmanTech\CommonUtils\Response;
use WebmanTech\LaravelCache\Facades\CacheRateLimiter;
use WebmanTech\LaravelCache\Middleware\ThrottleRequests;

test('limiter callback returns raw host response', function () {
    $rawResponse = new WebmanResponse();
    CacheRateLimiter::for('test_raw_response', fn () => $rawResponse);

    $middleware = new ThrottleRequests(['limiter_for' => 'test_raw_response']);
    $response = $middleware->process(new \stdClass(), fn () => Response::make());

    expect($response)->toBe($rawResponse);
});

test('limiter callback returns wrapped response', function () {
    $wrappedResponse = Response::make();
    CacheRateLimiter::for('test_wrapped_response', fn () => $wrappedResponse);

    $middleware = new ThrottleRequests(['limiter_for' => 'test_wrapped_response']);
    $response = $middleware->process(new \stdClass(), fn () => Response::make());

    expect($response)->toBeInstanceOf(WebmanResponse::class);
});
