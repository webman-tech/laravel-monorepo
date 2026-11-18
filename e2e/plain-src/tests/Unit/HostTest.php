<?php

use Illuminate\Container\Container;
use WebmanTech\CommonUtils\Constants;
use WebmanTech\CommonUtils\Runtime;
use WebmanTech\CommonUtils\Container as RuntimeContainer;

test('plain host basics', function () {
    expect(Runtime::getCurrent())->toBe(Constants::RUNTIME_CUSTOM);
    expect(Runtime::isWebman())->toBeFalse();

    // 宿主根目录（e2e target）
    expect(base_path())->toBe(realpath(__DIR__ . '/../..'));
    expect(config_path('app.php'))->toBe(base_path('config/app.php'));
    expect(runtime_path('cache'))->toBe(base_path('runtime/cache'));
    expect(vendor_path('autoload.php'))->toBe(base_path('vendor/autoload.php'));

    expect(config('app.debug'))->toBeTrue();
    expect(config('not_exist', 'default'))->toBe('default');

    $container = RuntimeContainer::getCurrent();
    expect($container->getRaw())->toBeInstanceOf(Container::class);
    expect(Container::getInstance())->toBeInstanceOf(Container::class);
});

test('config loaded with webman style keys', function () {
    expect(config('database.default'))->toBe('sqlite');
    expect(config('plugin.webman-tech.laravel-cache.cache.default'))->toBe('array');
});

test('locale helpers', function () {
    expect(locale())->toBe('zh_CN');
    locale('en');
    expect(locale())->toBe('en');
    locale('zh_CN');
    expect(locale())->toBe('zh_CN');
});
