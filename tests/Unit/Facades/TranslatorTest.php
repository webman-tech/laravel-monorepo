<?php

use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use WebmanTech\LaravelTranslation\Facades\Translator;


test('instance', function () {
    expect(Translator::instance())->toBeInstanceOf(TranslatorContract::class);
    expect(transL())->toBeInstanceOf(TranslatorContract::class);
    expect(__())->toEqual(null);
    // __() 不传返回 null
});

test('locale', function () {
    // 当前 locale
    expect(locale())->toEqual('zh_CN');

    // 由于未设置 fallback_locale，所以当 zh_CN 下没有该 key 时返回原始内容
    expect(trans('你好'))->toEqual('你好');
    // symfony 默认使用 domain 为 messages
    expect(transL('messages.你好'))->toEqual('messages.你好');

    // laravel 需要添加所属 group
    // 已有的翻译
    expect(trans('你好2'))->toEqual('你好');
    expect(transL('messages.你好2'))->toEqual('你好');

    // 用句子做为翻译的 key
    expect(trans('I love programming.'))->toEqual('我爱编程 From Message');
    // symfony 直接在 message 中写句子即可
    expect(transL('I love programming.'))->toEqual('我爱编程 From json');

    // laravel 需要写在 zh_CN.json 中
    // 指定语言文件
    expect(trans('key_from_app', [], 'app'))->toEqual('app中的翻译');
    expect(transL('app.key_from_app'))->toEqual('app中的翻译');

    // 设置 locale 为 en
    expect(locale('en'))->toEqual('en');

    // 已有的翻译
    expect(trans('你好'))->toEqual('hello');
    expect(transL('messages.你好'))->toEqual('hello');

    // 替换内容
    expect(trans('welcome0', ['%name%' => 'boy']))->toEqual('hello, boy');
    // symfony 正常使用 %name%
    expect(trans('welcome', [':name' => 'boy']))->toEqual('hello, boy');
    // symfony 也可以替换 :name
    expect(transL('messages.welcome', ['name' => 'boy']))->toEqual('hello, boy');
    // laravel 不需要带上 :
    expect(transL('messages.welcome2', ['name' => 'dayle']))->toEqual('hello, DAYLE');
    // laravel 自动转大写字母
    expect(transL('messages.welcome3', ['name' => 'dayle']))->toEqual('hello, Dayle');

    // laravel 自动转首字母大写
    // 负数处理
    expect(trans('apple_count', ['%count%' => 1]))->toEqual('There is one apple');
    // symfony 使用 %count%
    expect(trans('apple_count', ['%count%' => 10]))->toEqual('There are many apples');
    expect(trans('apple_count2', ['%count%' => 0]))->toEqual('There is no apple');
    expect(trans('apple_count2', ['%count%' => 1]))->toEqual('There is one apple');
    expect(trans('apple_count2', ['%count%' => 18]))->toEqual('There are 18 apples');
    expect(trans('apple_count2', ['%count%' => 25]))->toEqual('There are many apples');
    expect(trans_choice('messages.apple_count', 1))->toEqual('There is one apple');
    // laravel 使用 trans_choice
    expect(trans_choice('messages.apple_count', 3))->toEqual('There are many apples');
    expect(trans_choice('messages.apple_count3', 0))->toEqual('There is no apple');
    expect(trans_choice('messages.apple_count3', 1))->toEqual('There is one apple');
    expect(trans_choice('messages.apple_count3', 18, ['count' => 18]))->toEqual('There are 18 apples');
    expect(trans_choice('messages.apple_count3', 25))->toEqual('There are many apples');
});
