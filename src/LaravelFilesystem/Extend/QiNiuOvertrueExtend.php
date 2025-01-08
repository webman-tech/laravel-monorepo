<?php

namespace WebmanTech\LaravelFilesystem\Extend;

use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use Overtrue\Flysystem\Qiniu\QiniuAdapter;

/**
 * @link https://github.com/overtrue/laravel-filesystem-qiniu/blob/master/src/QiniuStorageServiceProvider.php
 */
class QiNiuOvertrueExtend implements ExtendInterface
{
    /**
     * @inheritDoc
     */
    public static function createExtend(array $config): FilesystemAdapter
    {
        $adapter = new QiniuAdapter(
            $config['access_key'],
            $config['secret_key'],
            $config['bucket'],
            $config['domain']
        );

        return new FilesystemAdapter(new Filesystem($adapter), $adapter, $config);
    }
}
