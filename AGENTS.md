# AGENTS.md

This file provides guidance to AI coding agents when working in this repository.

## 项目概述

将 Laravel `illuminate/*` 组件适配到 Webman 框架的 monorepo，同时支持非 webman 宿主（自研框架 / 标准 PHP 应用），webman 为第一支持优先级。包含 9 个独立包（`src/Laravel*`），通过 git-split 发布到各自仓库。版本号与 Laravel 主版本对齐（当前 13.x，要求 PHP >= 8.3）。

## 常用命令

```bash
composer test              # 运行测试（排除 HTTP 测试）
composer test-all          # 运行所有测试（含 HTTP，需要 httpbin 服务）
composer test-http         # 仅 HTTP 测试
composer test-coverage     # 测试覆盖率
composer analyse           # PHPStan 静态分析（level 6，phpVersion 8.3；内存不足时加 --memory-limit=512M）
composer analyse-raw       # PHPStan 原始输出格式

# 仅分析单个文件
./vendor/bin/phpstan analyse src/LaravelCache/Middleware/ThrottleRequestsWithRedis.php --memory-limit=512M

# 运行单个测试文件
./vendor/bin/pest tests/Unit/Facades/CacheTest.php

# 运行指定测试方法
./vendor/bin/pest --filter="test_method_name"

# e2e（需 PHP >= 8.3）
composer e2e:install       # 安装 webman + plain 两套宿主环境
composer e2e:test          # 依次运行 webman、plain 两套 e2e
```

本机默认 PHP 可能是 8.2，运行前需切到 8.3+（预览环境实测用 `/opt/homebrew/Cellar/php/8.4.8/bin/php`）。

## 架构

### 包结构

每个包（`src/Laravel*`）遵循统一结构：

- `Facades/` — 静态代理类，通过 `CommonUtils\Container` 访问容器中的单例；公共 API 通过 `@method` 注解暴露，注解由脚本生成
- `Helper/ConfigHelper.php` — 从 `config/plugin/webman-tech/{package}/` 读取配置
- `Helper/ExtComponentGetter.php` — 组件解析：容器实时探测优先（含宿主后绑），回退到默认实现 `BaseExtComponentGetter`
- `Mock/` — 实现 Laravel 接口的适配器（如 `LaravelApp` 实现 Container 接口）；`Default*Manager` 提供无 webman 时的兜底
- `Install.php` — Webman 插件安装/卸载，复制默认配置文件
- `copy/config/plugin/` — 安装时复制到项目的默认配置

### 核心设计

- Facade 单例懒加载，所有公共 API 通过 Facade 暴露
- `webman-tech/common-utils ^5.0` 提供容器与运行时抽象，webman / 非 webman 由 `Runtime` 判定，非 webman 需通过 `RuntimeCustomRegister` 注册容器、配置、日志、请求、定时器等能力
- 配置隔离在 `config/plugin/webman-tech/{package}/` 命名空间下
- webman 血系保持原生：DB 仍继承 `support\Db`（协程连接池行为不变），Redis 在 webman 下走 `WebmanRedisFactory`

### 包列表

LaravelCache, LaravelConsole, LaravelDatabase, LaravelFilesystem, LaravelHttp, LaravelProcess, LaravelRedis,
LaravelTranslation, LaravelValidation

## Monorepo 维护脚本（scripts/）

- `generate_composer.php` — 聚合所有包依赖到根 composer.json
- `generate_gitsplit.php` — 从包结构生成 .gitsplit.yml
- `generate_readme.php` — 更新根 README 的包列表
- `upgrade_laravel_version.php` — 批量升级 Laravel 版本（如 `php scripts/upgrade_laravel_version.php 12 13`）
- `transform_facade_comments.php` — 生成 Facade PHPDoc（`composer script:transform-facade-comments`）
- `generate_base_ext_component_getter.php` — 生成各包的 `BaseExtComponentGetter`（勿手改生成物）
- `replace_validation_files.php` — 更新验证语言文件

## CI

- 工作流在 `.github/workflows/`：`tests.yml`（phpunit + e2e）、`split.yml`（gitsplit）、`keep-actions-running.yml`
- 测试矩阵：PHP 8.3 / 8.4 / 8.5（Laravel 13 要求 >= 8.3）
- HTTP 测试依赖 go-httpbin 服务（CI 中 8080 端口）
- push 到 `*.x` 分支或发 tag 触发 git-split，拆分到 9 个独立仓库；`split.yml` 的 `ref` 需与各分支版本保持一致（13.x 分支应为 `13.x`）
- 定时任务由独立的 `scheduled-tests.yml` 承担（每日 UTC 0:00）：由于 `schedule` 事件只在默认分支（当前 `12.x`）触发，该文件用矩阵显式 checkout `12.x` 与 `13.x`，覆盖两个版本分支。`12.x` 跑 PHP 8.2/8.3/8.4/8.5，`13.x` 跑 8.3/8.4/8.5（Laravel 13 要求 >= 8.3）。`tests.yml` 只保留 push / PR 触发，两分支同版本需同步维护 `scheduled-tests.yml`

## 注意事项

- 修改包依赖后需运行 `php scripts/generate_composer.php` 同步根 composer.json
- 新增/删除包后需运行 `generate_gitsplit.php` 和 `generate_readme.php`
- 修改 Facade 的 `@method` 注解需改生成脚本再运行 `transform_facade_comments.php`，勿手改注解
- PHPStan 排除了 `LaravelDatabase/Larastan/*` 和各包的 `ArrayHelper.php`；level 6 的历史类型缺失集中在 `phpstan-baseline.neon`，新增代码不应再新增缺失类型
- 不要手动编辑 CHANGELOG（由发布流程管理）
