# webman-tech/laravel-http

Laravel [illuminate/http](https://packagist.org/packages/illuminate/http) 中的 HttpClient、Request、UploadedFile For webman

## 介绍

站在巨人（laravel）的肩膀上使 http 请求使用更加*可靠*和*便捷*

所有方法和配置与 laravel 几乎一模一样，因此使用方式完全参考 [Laravel httpClient 文档](https://laravel.com/docs/http-client) 和 [Laravel request 文档](https://laravel.com/docs/requests) 即可

## 安装

```bash
composer require webman-tech/laravel-http
```

## 使用

所有 API 同 laravel，以下仅对有些特殊的操作做说明

### Facade 入口

使用 `WebmanTech\LaravelHttpClient\Facades\Http` 代替 `Illuminate\Support\Facades\Http`
使用 `WebmanTech\LaravelHttpClient\Facades\LaravelRequest`（为了强调别名特意加的 Laravel 前缀） 代替 `Illuminate\Support\Facades\Request`
使用 `WebmanTech\LaravelHttpClient\Facades\LaravelUploadedFile` 来快速 wrapper `Webman\UploadedFile` 为 `Illuminate\Http\UploadedFile`
