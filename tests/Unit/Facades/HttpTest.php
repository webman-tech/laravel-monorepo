<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Tests\Fixtures\Constants;
use WebmanTech\LaravelHttpClient\Facades\Http;

beforeEach(function () {
    $this->httpBinHost = Constants::HTTP_BIN_HOST;
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
    // macro 已经通过 config 配置
    expect(Http::httpbin()->get('anything')->ok())->toBeTrue();
});

function ensureLogFile(bool $reset = true)
{
    $date = date('Y-m-d');
    $logFile = runtime_path() . "/logs/webman-{$date}.log";
    if (!file_exists($logFile)) {
        $dirname = dirname($logFile);
        if (!is_dir($dirname)) {
            mkdir(dirname($logFile), 0777, true);
        }
        file_put_contents($logFile, '');
    }
    if ($reset) {
        file_put_contents($logFile, '');
    }
    return $logFile;
}

test('log', function () {
    $logFile = ensureLogFile();
    $logSize = strlen(file_get_contents($logFile));

    $assetLogged = function (bool $is) use ($logFile, &$logSize) {
        $newLogSize = strlen(file_get_contents($logFile));
        // 通过比较日志大小来判断是否记了日志
        expect($is)->toBeBool(($newLogSize - $logSize) > 10);
        $logSize = $newLogSize;
    };

    // 直接发请求的记录日志
    Http::get("{$this->httpBinHost}/anything");
    $assetLogged(true);

    // macro 形式的记录日志
    Http::httpbin()->get('anything');
    $assetLogged(true);

    // pool 目前不会记录日志
    Http::pool(function (Pool $pool) {
        $pool->get("{$this->httpBinHost}/anything");
    });
    $assetLogged(false);
});

test('different formatter', function () {
    $logFile = ensureLogFile();

    Http::post("{$this->httpBinHost}/anything", [
        'a' => 'b'
    ]);

    // 人工检查日志格式是不是 psr 的
    expect(true)->toBeTrue();

    // 此处是在 config 中配置了 query 中有 use_json_formatter=1 的，使用 json 格式的日志
    Http::post("{$this->httpBinHost}/anything?use_json_formatter=1", [
        'password' => '123456'
    ]);

    // 人工检查日志格式是不是 json 的
    expect(true)->toBeTrue();

    // 检查 password 是否被替换
    $content = file_get_contents($logFile);
    $this->assertStringContainsString('\"password\":\"***\"', $content);

    Http::attach(
        'file', file_get_contents(__DIR__ . '/../../Fixtures/misc/test.txt')
    )->post("{$this->httpBinHost}/anything", [
        'a' => 'b'
    ]);

    // 人工检查日志是否将 form 的 body 屏蔽掉了
    expect(true)->toBeTrue();
});
