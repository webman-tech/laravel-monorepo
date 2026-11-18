# webman-tech/laravel-monorepo

laravel 组件的 monorepo 仓库，大版本随 laravel 的主版本号一致（当前 13.x，要求 PHP >= 8.3）。

开发约定与常用命令见 [AGENTS.md](./AGENTS.md)。

## 框架支持

所有组件均可运行在 webman（第一支持优先级） 与非 webman（自研框架 / 标准 PHP 应用）环境下。

通用接入模式：宿主启动时通过 `webman-tech/common-utils` 的 `RuntimeCustomRegister` 注册运行时能力
（容器、配置、日志、请求、定时器等），再将 db / redis 等 Manager 绑进容器即可。
完整示例见 `e2e/plain-src`（一个最小非 webman 宿主应用，随 e2e 测试自动安装运行，命令 `composer e2e:test`）。

## 安装

```bash
composer require webman-tech/laravel-monorepo
```

## 使用

参考各个组件的文档

## 组件列表

<!-- packages:start -->

- [webman-tech/laravel-cache](./src/LaravelCache/README.md)
- [webman-tech/laravel-console](./src/LaravelConsole/README.md)
- [webman-tech/laravel-database](./src/LaravelDatabase/README.md)
- [webman-tech/laravel-filesystem](./src/LaravelFilesystem/README.md)
- [webman-tech/laravel-http](./src/LaravelHttp/README.md)
- [webman-tech/laravel-process](./src/LaravelProcess/README.md)
- [webman-tech/laravel-redis](./src/LaravelRedis/README.md)
- [webman-tech/laravel-translation](./src/LaravelTranslation/README.md)
- [webman-tech/laravel-validation](./src/LaravelValidation/README.md)

<!-- packages:end -->

## 目录结构

- src: 各个组件目录
- scripts: 辅助 monorepo 的一些常用脚本
- tests: 测试目录
    - Fixtures: 测试数据，按照各个组件的目录
    - Unit: 单元测试
        - Facades: 基本对应每个组件的 facade 入口
    - webman: 用于单元测试的一个 webman 极小项目结构
- e2e: 端到端测试

## remark

- 11.x 12.x 13.x 是啥

与 laravel 的主版本保持一致

- 开发时如何从 12.x 升级为 13.x

使用脚本：`php scripts/upgrade_laravel_version.php 12 13`
