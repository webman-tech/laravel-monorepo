<?php

namespace WebmanTech\LaravelHttp\Mock;

use Illuminate\Http\Request as BaseRequest;
use Illuminate\Http\UploadedFile;
use WebmanTech\LaravelHttp\Facades\LaravelRequest;
use WebmanTech\LaravelHttp\Facades\LaravelUploadedFile;

/**
 * @internal
 */
final class Request extends BaseRequest
{
    /**
     * @inheritDoc
     */
    public static function capture()
    {
        static::enableHttpMethodParameterOverride();

        /** @var static $request */
        $request = LaravelRequest::getCalledFacadeClass()::createFromGlobal();
        return $request;
    }

    /**
     * @inheritDoc
     */
    public static function createFromGlobals(): static
    {
        /** @var static $request */
        $request = LaravelRequest::getCalledFacadeClass()::createFromGlobal();
        return $request;
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
