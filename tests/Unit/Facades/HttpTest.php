<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tests\Fixtures\HttpExtFacade;
use WebmanTech\LaravelHttp\Facades\Http;

pest()->group('http');

beforeEach(function () {
    $this->httpBinHost = config('plugin.webman-tech.laravel-http.app.httpbin_host');
});

test('instance', function () {
    expect(Http::instance())->toBeInstanceOf(HttpFactory::class);
});

test('http methods', function () {
    foreach (['get', 'post', 'patch', 'put', 'delete'] as $method) {
        $url = "{$this->httpBinHost}/{$method}";
        expect(Http::{$method}($url)['url'])->toEqual($url);
    }
});

function getHeader($data, $key): ?string
{
    $headerValues = Arr::wrap($data[$key]);
    return $headerValues[0] ?? null;
}

test('http status code', function () {
    $map = [
        200 => 'successful',
        310 => 'redirect',
        422 => ['failed', 'clientError'],
        500 => ['failed', 'serverError'],
    ];
    foreach ($map as $status => $resultFns) {
        $url = "{$this->httpBinHost}/status/{$status}";
        $response = Http::get($url);
        expect($response->status())->toEqual($status);
        foreach ((array)$resultFns as $fn) {
            expect($response->{$fn}() ? $fn : '')->toEqual($fn);
        }
    }
});

test('request data', function () {
    $data = [
        'name' => 'webman',
    ];

    // get query
    $response = Http::get("{$this->httpBinHost}/anything", $data);
    expect(Arr::wrap($response['args']['name'])[0] ?? null)->toEqual($data['name']);

    // post json
    $response = Http::post("{$this->httpBinHost}/anything", $data);
    expect($response['json'])->toEqual($data);

    // post form
    $response = Http::asForm()->post("{$this->httpBinHost}/anything", $data);
    expect(Arr::wrap($response['form']['name'])[0] ?? null)->toEqual($data['name']);

    // post rawBody
    $response = Http::withBody('xxxx', 'text/plain')->post("{$this->httpBinHost}/anything");
    expect($response['data'])->toEqual('xxxx');

    // post Multi-Part files
    $response = Http::attach(
        'file1', file_get_contents(__DIR__ . '/../../Fixtures/misc/test.txt'), 'test.txt',
    )->post("{$this->httpBinHost}/anything");
    expect(array_keys($response['files']))->toEqual(['file1']);
});

test('request headers', function () {
    // 自定义 header
    $response = Http::withHeaders([
        'X-First' => 'foo',
    ])->get("{$this->httpBinHost}/anything");
    expect(getHeader($response['headers'], 'X-First'))->toEqual('foo');

    // accept
    $response = Http::accept('text/html')->get("{$this->httpBinHost}/anything");
    expect(getHeader($response['headers'], 'Accept'))->toEqual('text/html');

    // acceptJson
    $response = Http::acceptJson()->get("{$this->httpBinHost}/anything");
    expect(getHeader($response['headers'], 'Accept'))->toEqual('application/json');
});

test('authentication', function () {
    // basic auth
    $response = Http::withBasicAuth('user', 'pass')->get("{$this->httpBinHost}/basic-auth/user/pass");
    expect($response->successful())->toBeTrue();

    $response = Http::withDigestAuth('user', 'pass')->get("{$this->httpBinHost}/digest-auth/auth/user/pass/MD5");
    expect($response->successful())->toBeTrue();

    // bearer
    $response = Http::withToken('token')->get("{$this->httpBinHost}/bearer");
    expect($response->successful())->toBeTrue();
});

test('pending', function () {
    // 以下情况不好测，仅确保方法存在
    expect(Http::timeout(3))->toBeInstanceOf(PendingRequest::class);
    expect(Http::retry(3))->toBeInstanceOf(PendingRequest::class);
    expect(Http::retry(3, 10))->toBeInstanceOf(PendingRequest::class);
    expect(Http::retry(3, 10, function ($e) {
        return $e instanceof ConnectionException;
    }))->toBeInstanceOf(PendingRequest::class);
});

test('error handling', function () {
    $response = Http::get("{$this->httpBinHost}/status/500");

    // onError
    $response->onError(function () {
        expect(true)->toBeTrue();
    });

    // throw
    try {
        $response->throw();
    } catch (\Throwable $e) {
        expect($e)->toBeInstanceOf(RequestException::class);
    }

    // throwIf
    try {
        $response->throwIf(true);
    } catch (\Throwable $e) {
        expect($e)->toBeInstanceOf(RequestException::class);
    }
    // 其他 throwUnless throwIfStatus 不写了
});

test('pool', function () {
    $responses = Http::pool(function (Pool $pool) {
        return [
            $pool->get("{$this->httpBinHost}/anything"),
            $pool->get("{$this->httpBinHost}/anything"),
        ];
    });
    expect($responses[0]->ok() && $responses[1]->ok())->toBeTrue();

    $responses = Http::pool(function (Pool $pool) {
        return [
            $pool->as('first')->get("{$this->httpBinHost}/anything"),
            $pool->as('second')->get("{$this->httpBinHost}/anything"),
        ];
    });
    expect($responses['first']->ok() && $responses['second']->ok())->toBeTrue();
});

test('macro', function () {
    $httpBinHost = $this->httpBinHost;
    Http::macro('httpbin', function () use ($httpBinHost) {
        return Http::baseUrl($httpBinHost);
    });

    expect(Http::httpbin()->get('anything')->ok())->toBeTrue();
});

test('middleware', function () {
    $response = Http::withRequestMiddleware(function (RequestInterface $request) {
        return $request->withHeader('X-First', 'foo');
    })
        ->withResponseMiddleware(function (ResponseInterface $response) {
            $content = json_decode($response->getBody(), true);
            expect(getHeader($content['headers'], 'X-First'))->toEqual('foo');

            return $response;
        })
        ->get("{$this->httpBinHost}/anything");
    expect(getHeader($response['headers'], 'X-First'))->toEqual('foo');
});

test('boot', function () {
    $response = HttpExtFacade::httpbin()->get('anything');

    expect($response->ok())->toBeTrue()
        ->and(getHeader($response['headers'], 'X-Global-Header'))->toEqual('foo');
});
