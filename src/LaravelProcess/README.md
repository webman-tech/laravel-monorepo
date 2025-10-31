# webman-tech/laravel-process

> Split from [webman-tech/laravel-monorepo](https://github.com/webman-tech/laravel-monorepo)

适用于 webman 的 Laravel 进程组件，基于 illuminate/process 实现。

## 安装

```bash
composer require webman-tech/laravel-process
```

## 简介

该组件将 Laravel 强大的进程管理功能引入 webman 框架中，提供了便捷的进程执行和管理功能。

所有方法和配置与 Laravel 几乎一致，因此使用方式可完全参考 [Laravel Process 文档](https://laravel.com/docs/processes)。

## 特殊使用说明

### 1. Facades 使用方式

使用 `WebmanTech\LaravelProcess\Facades\Process` 替代 `Illuminate\Support\Facades\Process`
