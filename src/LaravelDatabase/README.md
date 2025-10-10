# webman-tech/laravel-database

> Split from [webman-tech/laravel-monorepo](https://github.com/webman-tech/laravel-monorepo)

对 [webman/database](https://www.workerman.net/doc/webman/db/tutorial.html) 的包装

## 使用

`WebmanTech\LaravelDatabase\Facades\DB` 提供更完整的代码提示

```php
use WebmanTech\LaravelDatabase\Facades\DB;

DB::table('users')->get();
```

## 支持 phpstan 检查

### 安装

在 `phpstan.neon` 或 `phpstan.neon.dist` 中添加 `includes` 配置:

```neon
includes:
    - vendor/webman-tech/laravel-monorepo/src/LaravelDatabase/Larastan/extension.neon
```

### 检查

```bash
./vendor/bin/phpstan analyse
```
