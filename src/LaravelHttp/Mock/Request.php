<?php

namespace WebmanTech\LaravelHttp\Mock;

use Illuminate\Http\Request as BaseRequest;
use Illuminate\Http\UploadedFile;
use WebmanTech\LaravelHttp\Facades\LaravelUploadedFile;

/**
 * @internal
 * @method static static createFromWebman(\Webman\Http\Request|null $request = null, bool $isForceCreate = false)
 */
final class Request extends BaseRequest
{
    /**
     * @inheritDoc
     */
    public static function capture()
    {
        static::enableHttpMethodParameterOverride();

        return static::createFromWebman(null, true);
    }

    /**
     * @inheritDoc
     */
    public static function createFromGlobals(): static
    {
        return static::createFromWebman(null, true);
    }

    /**
     * @inheritDoc
     */
    protected function convertUploadedFiles(array $files): array
    {
        return array_map(function (UploadedFile $file) {
            return LaravelUploadedFile::wrapper($file);
        }, parent::convertUploadedFiles($files));
    }
}
