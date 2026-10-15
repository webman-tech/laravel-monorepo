<?php

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Webman\Http\Request as WebmanRequest;
use WebmanTech\CommonUtils\RuntimeCustomRegister;
use WebmanTech\LaravelHttp\Facades\LaravelRequest;
use WebmanTech\LaravelHttp\Mock\Request;

test('instance', function () {
    expect(LaravelRequest::instance())->toBeInstanceOf(Request::class);
});

test('extend facade to override createFromGlobal', function () {
    $requestClass = new class extends LaravelRequest {
        public static function createFromGlobal(): \Illuminate\Http\Request
        {
            return Request::create('/custom', 'POST', ['foo' => 'bar']);
        }
    };

    // instance() 经 static::createFromGlobal() 调用，走子类覆盖实现
    expect($requestClass::instance()->input('foo'))->toBe('bar')
        // __callStatic 亦经 instance() 走覆盖实现
        ->and($requestClass::method())->toBe('POST')
        // capture/createFromGlobals 为 Facade 真实方法：若经 __callStatic 落到 illuminate 原生实现会绕过覆盖直接读 PHP superglobals
        ->and($requestClass::capture()->input('foo'))->toBe('bar')
        ->and($requestClass::createFromGlobals()->input('foo'))->toBe('bar');

    // 实例调用路径：instance() 后在实例上调用 capture/createFromGlobals，
    // 经 calledFacadeClass 记录回溯子类覆盖实现（Mock\Request 内为硬编码转发，无 late static binding）
    $request = $requestClass::instance();
    expect($request)->toBeInstanceOf(Request::class)
        ->and($request->capture()->input('foo'))->toBe('bar')
        ->and($request->createFromGlobals()->input('foo'))->toBe('bar');
});

test('instance from symfony request', function () {
    RuntimeCustomRegister::register(
        RuntimeCustomRegister::KEY_REQUEST,
        fn () => SymfonyRequest::create('/demo?foo=bar', 'POST', ['name' => 'kriss'], [], [], ['HTTP_HOST' => 'demo.example.com']),
    );
    try {
        $request = LaravelRequest::instance();
        expect($request)->toBeInstanceOf(Request::class)
            ->and($request->method())->toBe('POST')
            ->and($request->input('name'))->toBe('kriss')
            ->and($request->query('foo'))->toBe('bar')
            ->and($request->host())->toBe('demo.example.com');
    } finally {
        RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_REQUEST, null);
    }
});

test('instance from laravel request', function () {
    RuntimeCustomRegister::register(
        RuntimeCustomRegister::KEY_REQUEST,
        fn () => \Illuminate\Http\Request::create('/demo?foo=bar', 'PUT', ['name' => 'kriss']),
    );
    try {
        $request = LaravelRequest::instance();
        expect($request)->toBeInstanceOf(Request::class)
            ->and($request->method())->toBe('PUT')
            ->and($request->input('name'))->toBe('kriss')
            ->and($request->query('foo'))->toBe('bar');
    } finally {
        RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_REQUEST, null);
    }
});

test('mock request capture', function () {
    // webman CLI 下无请求，回退空 Request
    $request = Request::capture();
    expect($request)->toBeInstanceOf(Request::class)
        ->and($request->getRequestUri())->toBe('/');

    $request = Request::createFromGlobals();
    expect($request)->toBeInstanceOf(Request::class)
        ->and($request->getRequestUri())->toBe('/');
});

test('createFromWebman', function () {
    $request = LaravelRequest::createFromWebman();
    expect($request)->toBeInstanceOf(Request::class);

    // get
    $webmanRequest = new WebmanRequest(get_misc_content('http/get1.txt'));
    $request = LaravelRequest::createFromWebman($webmanRequest);
    expect($request->isMethod('get'))
        ->and($request->get('query'))->toEqual('chatgpt')
        ->and($request->get('page'))->toEqual('2')
        ->and($request->host())->toEqual('www.example.com')
        ->and($request->userAgent())->toEqual('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36')
        ->and($request->accepts('application/xml'))->toBeTrue()
        ->and($request->header('accept-encoding'))->toEqual('gzip, deflate, br');

    // post
    $webmanRequest = new WebmanRequest(get_misc_content('http/post1.txt'));
    $request = LaravelRequest::createFromWebman($webmanRequest);
    expect($request->isMethod('post'))
        ->and($request->post('name'))->toEqual('John Doe')
        ->and($request->post('email'))->toEqual('john.doe@example.com');
});
