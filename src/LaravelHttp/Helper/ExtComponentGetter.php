<?php

namespace WebmanTech\LaravelHttp\Helper;

use Illuminate\Container\Container as LaravelContainer;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Events\Dispatcher;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use support\Container;
use WebmanTech\LaravelFilesystem\Facades\Storage;
use WebmanTech\LaravelValidation\Facades\Validator;

/**
 * @internal
 */
final class ExtComponentGetter
{
    private static array $componentsDefine = [];
    private static array $components = [];

    private static function getDefinedComponents(): array
    {
        if (!self::$componentsDefine) {
            $define = [
                /** @see FilesystemServiceProvider::registerManager() */
                FilesystemFactory::class => [
                    'alias' => ['filesystem'],
                    'singleton' => fn() => class_exists(Storage::class)
                        ? Storage::instance()
                        : throw new \InvalidArgumentException('install `webman-tech/laravel-filesystem` first'),
                ],
                /** @see ValidationServiceProvider::registerValidationFactory() */
                ValidatorFactory::class => [
                    'alias' => ['validator'],
                    'singleton' => fn() => class_exists(Validator::class)
                        ? fn() => Validator::instance()
                        : throw new \InvalidArgumentException('install `webman-tech/laravel-validator` first'),
                ],
                /** @see EventServiceProvider::register() */
                DispatcherContract::class => [
                    'alias' => ['events'],
                    'singleton' => fn() => new Dispatcher(LaravelContainer::getInstance()),
                ],
            ];

            foreach ($define as $className => $options) {
                $ids = array_merge([$className], $options['alias'] ?? []);
                $componentGetter = null;
                foreach ($ids as $id) {
                    if (Container::has($id)) {
                        $componentGetter = fn() => Container::get($id);
                        break;
                    }
                }
                if ($componentGetter === null) {
                    $componentGetter = $options['singleton'] ?? null;
                }
                if ($componentGetter === null) {
                    throw new \InvalidArgumentException('cannot find component ' . $className);
                }
                // 将所有 id 注册为组件
                foreach ($ids as $id) {
                    self::$componentsDefine[$id] = [$ids, $componentGetter];
                }
            }
        }

        return self::$componentsDefine;
    }

    /**
     * @template T of class-string
     * @param T $needClass
     * @return T
     */
    public static function get(string $needClass)
    {
        $component = self::$components[$needClass] ?? null;
        if ($component) {
            return $component;
        }

        $componentDefine = self::getDefinedComponents()[$needClass] ?? null;
        if ($componentDefine === null) {
            throw new \InvalidArgumentException($needClass . ' is not defined');
        }

        [$ids, $componentGetter] = $componentDefine;
        $component = $componentGetter();
        if ($component === null) {
            throw new \InvalidArgumentException($needClass . ' fetch from componentGetter is null');
        }
        // 将所有相关组件都注册上
        foreach ($ids as $id) {
            self::$components[$id] = $component;
        }

        return $component;
    }
}
