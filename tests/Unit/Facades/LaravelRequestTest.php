<?php

use Webman\Http\Request as WebmanRequest;
use WebmanTech\LaravelHttp\Facades\LaravelRequest;
use WebmanTech\LaravelHttp\Request;

test('instance', function () {
    expect(LaravelRequest::instance())->toBeInstanceOf(Request::class);
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
