# webman-tech/laravel-database

> Split from [webman-tech/laravel-monorepo](https://github.com/webman-tech/laravel-monorepo)

适用于 webman 与非 webman 环境的 Laravel 数据库组件，基于 illuminate/database 实现。

## 安装

```bash
composer require webman-tech/laravel-database
```

## 简介

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

## 非 webman 环境接入

本组件不强制依赖 webman。`Facades\DB` 的解析顺序：

1. 容器中已绑定 `Illuminate\Database\ConnectionResolverInterface`，直接使用（需要完全控制时推荐）
2. 否则回退 webman 的 `support\Db`（需安装 `webman/database`）
3. 否则使用纯 illuminate 默认实现：从 `config('database')` 创建 `DatabaseManager`（连接配置缺失时在使用时报错）

非 webman 宿主仅需提供 `config('database')` 即可使用；需要完全控制（自定义连接池、事件监听等）时在启动时将自己的
DatabaseManager 绑进容器（完整示例见仓库根 README 与 `e2e/plain-src`）。

webman 环境下无需任何改动，Facade 自动走 `support\Db`（协程连接池行为不变）。
