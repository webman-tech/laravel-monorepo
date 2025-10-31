# webman-tech/laravel-database

> Split from [webman-tech/laravel-monorepo](https://github.com/webman-tech/laravel-monorepo)

适用于 webman 的 Laravel 数据库组件，基于 illuminate/database 实现。

## 安装

```bash
composer require webman-tech/laravel-database
```

## 简介

该组件将 Laravel 强大的数据库功能引入 webman 框架中，提供了完整的数据库查询构建器和 Eloquent ORM。

所有方法和配置与 Laravel 几乎一致，因此使用方式可完全参考 [Laravel Database 文档](https://laravel.com/docs/database)。

## 特殊使用说明

### 1. Facades 使用方式

使用 `WebmanTech\LaravelDatabase\Facades\DB` 提供更完整的代码提示：

```php
use WebmanTech\LaravelDatabase\Facades\DB;

DB::table('users')->get();
```

### 2. Larastan 静态分析支持

在 `phpstan.neon` 或 `phpstan.neon.dist` 中添加：

```neon
includes:
    - vendor/webman-tech/laravel-monorepo/src/LaravelDatabase/Larastan/extension.neon
```

运行分析：

```bash
./vendor/bin/phpstan analyse
```
