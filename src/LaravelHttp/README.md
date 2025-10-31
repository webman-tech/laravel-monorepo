# webman-tech/laravel-http

> Split from [webman-tech/laravel-monorepo](https://github.com/webman-tech/laravel-monorepo)

适用于 webman 的 Laravel HTTP 组件，基于 illuminate/http 实现。

## 安装

```bash
composer require webman-tech/laravel-http
```

## 简介

该组件将 Laravel 强大的 HTTP 功能引入 webman 框架中，包括 HTTP 客户端、Request 对象和上传文件处理。

所有方法和配置与 Laravel
几乎一致，因此使用方式可完全参考 [Laravel HTTP Client 文档](https://laravel.com/docs/http-client)
和 [Laravel Requests 文档](https://laravel.com/docs/requests)。

## 特殊使用说明

### 1. Facades 使用方式

- 使用 `WebmanTech\LaravelHttp\Facades\Http` 替代 `Illuminate\Support\Facades\Http`
- 使用 `WebmanTech\LaravelHttp\Facades\LaravelRequest` 替代 `Illuminate\Support\Facades\Request`
- 使用 `WebmanTech\LaravelHttp\Facades\LaravelUploadedFile` 来快速包装 `Webman\UploadedFile` 为
  `Illuminate\Http\UploadedFile`

### 2. 文件上传处理

```php
use WebmanTech\LaravelHttp\Facades\LaravelUploadedFile;

// 包装 Webman 上传文件为 Laravel UploadedFile
$uploadedFile = LaravelUploadedFile::wrapper($request->file('avatar'));
// 现在可以使用 Laravel 的所有文件操作方法
$path = $uploadedFile->store('avatars');
```
