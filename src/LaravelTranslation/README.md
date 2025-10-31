# webman-tech/laravel-translation

> Split from [webman-tech/laravel-monorepo](https://github.com/webman-tech/laravel-monorepo)

适用于 webman 的 Laravel 翻译组件，基于 illuminate/translation 实现。

## 安装

```bash
composer require webman-tech/laravel-translation
```

## 简介

该组件将 Laravel 强大的翻译和本地化功能引入 webman 框架中，提供了多语言支持和参数替换等功能。

所有方法和配置与 Laravel
几乎一致，因此使用方式可完全参考 [Laravel Localization 文档](https://laravel.com/docs/localization)。

## 特殊使用说明

### 翻译函数名称

由于 webman 下默认使用 `symfony/translation`，且已经定义过 trans 方法，为了不冲突，此处使用 `transL()`：

```php
$message = transL('messages.welcome');
```

### 语言切换

因为没有 Laravel App 的存在，所以不能通过 `App::setLocale()` 和 `App::currentLocale()` 来切换语言。

本扩展已经做到根据 `locale()` 自动切换 `transL()`、`trans_choice()`、`__()` 下使用的语言包，无需开发手动设置。

### 基本使用

```php
// 使用 transL()
$message1 = transL('messages.abc');

// 使用 trans_choice()
$message2 = trans_choice('messages.xyz', 2);

// 使用 __()
$message3 = __('messages.mnl');
```
