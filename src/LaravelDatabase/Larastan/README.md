# From Larastan

当前目录下的内容是从 Larastan（https://github.com/larastan/larastan/tree/v3.7.2） 提取来的，仅修改了命名空间。

由于 larastan 较大，且目标是完整的 laravel 项目，此处我们提取了 Eloquent 的部分，使得 phpstan 能够正确解析以下情况

```php
use PHPStan\dumpType;

$model = Admin::query()->find($id);
dumpType($model); // Admin
dumpType($model->username); // string

$models = Admin::query()->whereIn('id', ['1', '2'])->get();
dumpType($models); // Collection<Admin>
dumpType($models->first()->username); // string
```
