# webman-tech/laravel-monorepo

适用于 webman 的 laravel 组件的 monorepo 仓库，大版本随 laravel 的主版本号一致

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
- [webman-tech/laravel-redis](./src/LaravelRedis/README.md)
- [webman-tech/laravel-translation](./src/LaravelTranslation/README.md)
- [webman-tech/laravel-validation](./src/LaravelValidation/README.md)

<!-- packages:end -->

## remark

- 11.x 12.x 是啥

与 laravel 的主版本保持一致

- 开发时如何从 11.x 升级为 12.x

使用脚本：`php scripts/upgrade_laravel_version.php 11 12`
