# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 项目概述

将 Laravel illuminate/* 组件适配到 Webman 框架的 monorepo，包含 9 个独立包（`src/Laravel*`），通过 git-split 发布到各自的独立仓库。版本号与
Laravel 主版本对齐（当前 12.x）。

## 常用命令

```bash
composer test              # 运行测试（排除 HTTP 测试）
composer test-all          # 运行所有测试（含 HTTP，需要 httpbin 服务）
composer test-http         # 仅 HTTP 测试
composer test-coverage     # 测试覆盖率
composer analyse           # PHPStan 静态分析（level 5），内存不足时加 --memory-limit=512M
composer analyse-raw       # PHPStan 原始输出格式

# 仅分析单个文件
./vendor/bin/phpstan analyse src/LaravelCache/Middleware/ThrottleRequestsWithRedis.php --memory-limit=512M

# 运行单个测试文件
./vendor/bin/pest tests/Unit/Facades/CacheTest.php

# 运行指定测试方法
./vendor/bin/pest --filter="test_method_name"
```

## 架构

### 包结构

每个包（`src/Laravel*`）遵循统一结构：

- `Facades/` — 静态代理类，通过 `CommonUtils\Container` 访问 Webman 容器中的单例
- `Helper/ConfigHelper.php` — 从 `config/plugin/webman-tech/{package}/` 读取配置
- `Helper/ExtComponentGetter.php` — 组件解析：优先容器，回退到默认实现
- `Mock/` — 实现 Laravel 接口的适配器（如 `LaravelApp` 实现 Container 接口）
- `Install.php` — Webman 插件安装/卸载，复制默认配置文件
- `copy/config/plugin/` — 安装时复制到项目的默认配置

### 核心设计

- Facade 单例懒加载，所有公共 API 通过 Facade 暴露
- `webman-tech/common-utils ^5.0` 提供容器抽象，减少对 webman 的直接依赖
- 配置隔离在 `config/plugin/webman-tech/{package}/` 命名空间下

### 包列表

LaravelCache, LaravelConsole, LaravelDatabase, LaravelFilesystem, LaravelHttp, LaravelProcess, LaravelRedis,
LaravelTranslation, LaravelValidation

## Monorepo 维护脚本（scripts/）

- `generate_composer.php` — 聚合所有包依赖到根 composer.json
- `generate_gitsplit.php` — 从包结构生成 .gitsplit.yml
- `generate_readme.php` — 更新根 README 的包列表
- `upgrade_laravel_version.php` — 批量升级 Laravel 版本
- `transform_facade_comments.php` — 生成 Facade PHPDoc（`composer script:transform-facade-comments`）
- `replace_validation_files.php` — 更新验证语言文件

## CI

- 测试矩阵：PHP 8.2 / 8.3 / 8.4
- HTTP 测试依赖 go-httpbin 服务（CI 中在 8080 端口）
- push 到 `*.x` 分支或发布 tag 时触发 git-split，拆分到 9 个独立仓库

## 注意事项

- 修改包的依赖后需运行 `php scripts/generate_composer.php` 同步根 composer.json
- 新增/删除包后需运行 `generate_gitsplit.php` 和 `generate_readme.php`
- PHPStan 排除了 `LaravelDatabase/Larastan/*` 和各包的 `ArrayHelper.php`
