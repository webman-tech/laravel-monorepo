# Changelog

All notable changes to this project will be documented in this file. See [standard version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

## [Unreleased]

## [12.0.14] - 2026-10-12

### Added
- **cache**: 实现 Watchdog Lock 自动续期机制

### Fixed
- 依赖版本问题
- **cache**: 修复 watchdog 锁续期异常导致 worker 重启与泄漏锁

## [12.0.13] - 2026-04-24

### Fixed
- **cache**: ThrottleRequestsWithRedis 改为按需获取 Redis 连接，适配连接池
- **console**: 同步 artisan 启动脚本与 webman/console 最新版本对齐

[12.0.13]: https://github.com/webman-tech/laravel-monorepo/compare/v12.0.12...v12.0.13
[12.0.14]: https://github.com/webman-tech/laravel-monorepo/compare/v12.0.13...v12.0.14
[Unreleased]: https://github.com/webman-tech/laravel-monorepo/compare/v12.0.14...HEAD
