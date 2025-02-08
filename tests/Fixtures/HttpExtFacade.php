<?php

namespace Tests\Fixtures;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Psr\Http\Message\RequestInterface;
use WebmanTech\LaravelHttp\Facades\Http;

/**
 * @method static PendingRequest httpbin()
 */
class HttpExtFacade extends Http
{
    protected static ?Factory $instance = null; // 为了与 Http 的实例分开

    public static function getAllMacros(): array
    {
        return [
            'httpbin' => function () {
                return self::baseUrl(config('plugin.webman-tech.laravel-http.app.httpbin_host'))
                    ->asJson();
            },
        ];
    }

    protected static function boot(Factory $factory): void
    {
        parent::boot($factory);

        $factory->globalRequestMiddleware(fn (RequestInterface $request) => $request->withHeader(
            'X-Global-Header', 'foo'
        ));
    }
}
