<?php

namespace Tests\Fixtures;

use Illuminate\Http\Client\Factory;
use Psr\Http\Message\RequestInterface;
use WebmanTech\LaravelHttp\Facades\Http;

class HttpExtFacade extends Http
{
    protected static function boot(Factory $factory): void
    {
        $factory->globalRequestMiddleware(fn (RequestInterface $request) => $request->withHeader(
            'X-Global-Header', 'foo'
        ));

        $factory::macro('httpbin', function () use ($factory) {
            return $factory->baseUrl(config('plugin.webman-tech.laravel-http.app.httpbin_host'));
        });
    }
}
