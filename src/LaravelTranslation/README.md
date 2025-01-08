# webman-tech/laravel-translation

Laravel [illuminate/translation](https://packagist.org/packages/illuminate/translation) for webman

## 介绍

站在巨人（laravel）的肩膀上使本地化使用更加*可靠*和*便捷*

所有方法和配置与 laravel 几乎一模一样，因此使用方式完全参考 [Laravel文档](https://laravel.com/docs/translation) 即可

## 安装

```bash
composer require webman-tech/laravel-translation
```

## 使用

所有 API 同 laravel，以下仅对有些特殊的操作做说明

常规使用如下：

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function bar(Request $request) 
    {
        $message1 = transL('messages.abc');
        $message2 = trans_choice('messages.xyz', 2);
        $message3 = __('messages.mnl');
        return json([
            $message1, $message2, $message3
        ]);
    }
}
```

### tranL()

由于 webman 下默认使用 `symfony/translation`，且已经定义过 trans 方法，为了不冲突，此处使用 `transL()`

### 手动切换 locale

因为没有 Laravel App 的存在，所以不能通过 `App::setLocale()` 和 `App::currentLocale()` 来切换语言

且由于 webman 建议的多语言是使用的 `symfony/translation`，并且全局 `locale` 函数也是使用其实现的

因此本扩展基于此原因，已经做到了根据 `locale()` 自动切换 `transL()` `trans_choice()` `__()` 下使用的语言包，无需开发手动设置
