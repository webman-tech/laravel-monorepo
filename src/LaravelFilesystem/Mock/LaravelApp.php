<?php

namespace WebmanTech\LaravelFilesystem\Mock;

use Illuminate\Support\Fluent;

/**
 * 仅实现当前包中需要用到的 laravel app 的方法
 * @internal
 */
final class LaravelApp extends Fluent
{
    public function value($key, $default = null)
    {
        if ($key === 'url') {
            $value = parent::value($key, $default);
            if (is_callable($value)) {
                $value = $value();
                parent::set($key, $value);
            }
        }

        return parent::value($key, $default);
    }
}
