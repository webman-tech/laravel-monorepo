# webman-tech/laravel-redis

> Split from [webman-tech/laravel-monorepo](https://github.com/webman-tech/laravel-monorepo)

适用于 webman 的 Laravel Redis 组件，基于 illuminate/redis 实现。

## 安装

```bash
composer require webman-tech/laravel-redis
```

## 简介

该组件是对 [webman/redis](https://www.workerman.net/doc/webman/db/redis.html) 的包装，提供更完整的 Laravel Redis 使用体验。

所有方法和配置与 Laravel 几乎一致，因此使用方式可完全参考 [Laravel Redis 文档](https://laravel.com/docs/redis)。

## 特殊使用说明

使用 `WebmanTech\LaravelRedis\Facades\Redis` 替代 `Illuminate\Support\Facades\Redis`

```php
use WebmanTech\LaravelRedis\Facades\Redis;

Redis::set('key', 'value');
$value = Redis::get('key');
```
