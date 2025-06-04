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
use WebmanTech\LaravelFilesystem\Facades\Storage;
use WebmanTech\LaravelValidation\Facades\Validator;

/**
 * @internal
 */
final class ExtComponentGetter extends BaseExtComponentGetter
{
    protected static function getDefine(): array
    {
        return [
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
    }
}
