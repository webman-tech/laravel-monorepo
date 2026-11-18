<?php

use WebmanTech\LaravelCache\Facades\Cache;
use WebmanTech\LaravelCache\Facades\CacheRateLimiter;
use WebmanTech\LaravelHttp\Facades\LaravelRequest;
use WebmanTech\LaravelValidation\Facades\Validator;

test('cache facade without webman', function () {
    $cache = Cache::instance();
    expect($cache)->toBeInstanceOf(\Illuminate\Cache\CacheManager::class);

    $cache->set('plain-key', 'plain-value', 60);
    expect($cache->get('plain-key'))->toBe('plain-value');
    expect($cache->has('plain-key'))->toBeTrue();
    $cache->forget('plain-key');
    expect($cache->has('plain-key'))->toBeFalse();

    expect(CacheRateLimiter::instance())->toBeInstanceOf(\Illuminate\Cache\RateLimiter::class);
});

test('validator facade', function () {
    $validator = Validator::make(['name' => 'kriss'], ['name' => 'required|string']);
    expect($validator->passes())->toBeTrue();
    expect($validator->errors()->all())->toBe([]);

    $validator = Validator::make(['name' => ''], ['name' => 'required']);
    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('name'))->toBe('name 不能为空。');
});

test('laravel request facade without webman request', function () {
    $request = LaravelRequest::instance();
    expect($request)->toBeInstanceOf(\WebmanTech\LaravelHttp\Mock\Request::class);
});

test('laravel request facade with symfony request', function () {
    // 宿主注册的原始请求为 Symfony 请求时，createFromBase 轻量转换
    \WebmanTech\CommonUtils\RuntimeCustomRegister::register(
        \WebmanTech\CommonUtils\RuntimeCustomRegister::KEY_REQUEST,
        fn () => \Symfony\Component\HttpFoundation\Request::create('/plain/demo?foo=bar', 'POST', ['name' => 'kriss'], [], [], ['HTTP_HOST' => 'plain.example.com']),
    );
    try {
        $request = LaravelRequest::instance();
        expect($request)->toBeInstanceOf(\WebmanTech\LaravelHttp\Mock\Request::class)
            ->and($request->method())->toBe('POST')
            ->and($request->input('name'))->toBe('kriss')
            ->and($request->query('foo'))->toBe('bar')
            ->and($request->host())->toBe('plain.example.com');
    } finally {
        \WebmanTech\CommonUtils\RuntimeCustomRegister::register(
            \WebmanTech\CommonUtils\RuntimeCustomRegister::KEY_REQUEST,
            fn () => \Symfony\Component\HttpFoundation\Request::createFromGlobals(),
        );
    }
});

test('request create from globals', function () {
    $request = \WebmanTech\LaravelHttp\Mock\Request::createFromGlobals();
    expect($request)->toBeInstanceOf(\WebmanTech\LaravelHttp\Mock\Request::class);
});

test('uploaded file wrapper without webman origin', function () {
    $tmp = tempnam(sys_get_temp_dir(), 'plain-upload');
    file_put_contents($tmp, 'plain upload content');

    $symfonyFile = new \Symfony\Component\HttpFoundation\File\UploadedFile(
        $tmp, 'plain.txt', 'text/plain', null, true
    );
    $wrapped = \WebmanTech\LaravelHttp\Facades\LaravelUploadedFile::wrapper($symfonyFile);
    expect($wrapped)->toBeInstanceOf(\WebmanTech\LaravelHttp\Facades\LaravelUploadedFile::class);
    expect($wrapped->getClientOriginalName())->toBe('plain.txt');
    expect($wrapped->isValid())->toBeTrue();

    @unlink($tmp);
});
