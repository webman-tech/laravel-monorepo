<?php

return [
    'default' => 'local',
    'cloud' => 'cloud',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => base_path() . '/storage/test-storage',
            'throw' => false,
        ],
        'public' => [
            'driver' => 'local',
            'root' => base_path() . '/storage/app/public',
            'url' => (getenv('APP_URL') ?: '') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
        'cloud' => [
            'driver' => 'local',
            'root' => base_path() . '/storage/app',
            'throw' => false,
        ],
        'qiniu' => [
            'driver' => \WebmanTech\LaravelFilesystem\Extend\QiNiuOvertrueExtend::class,
            'access_key' => getenv('QINIU_ACCESS_KEY') ?: 'xxxxxxxxxxxxxxxx',
            'secret_key' => getenv('QINIU_SECRET_KEY') ?: 'xxxxxxxxxxxxxxxx',
            'bucket' => getenv('QINIU_BUCKET') ?: 'test',
            'domain' => getenv('QINIU_DOMAIN') ?: 'xxx.clouddn.com',
        ],
        'cos' => [
            'driver' => \WebmanTech\LaravelFilesystem\Extend\CosOvertrueExtend::class,
            'app_id' => getenv('COS_APP_ID'),
            'secret_id' => getenv('COS_SECRET_ID'),
            'secret_key' => getenv('COS_SECRET_KEY'),
            'region' => getenv('COS_REGION') ?: 'ap-guangzhou',
            'bucket' => getenv('COS_BUCKET'),
            'signed_url' => false,
            'use_https' => true,
            'domain' => 'example-12340000.cos.test.com',
            'guzzle' => [
                'timeout' => 60,
                'connect_timeout' => 60,
            ],
        ],
        'oss' => [
            'driver' => \WebmanTech\LaravelFilesystem\Extend\OssAlphaSnowExtend::class,
            'access_key_id' => getenv('OSS_ACCESS_KEY_ID') ?: 'xxx',
            'access_key_secret' => getenv('OSS_ACCESS_KEY_SECRET') ?: 'xxx',
            'bucket' => getenv('OSS_BUCKET') ?: 'my-storage',
            'endpoint' => getenv('OSS_ENDPOINT') ?: 'oss-cn-shanghai.aliyuncs.com',
            'macros' => [],
        ],
    ],
    'links' => [
        base_path() . '/public/storage' => base_path() . '/storage/app/public',
    ],
    'extends' => [
        'cloud' => function ($app, array $config) {
            $factory = \WebmanTech\LaravelFilesystem\Facades\Storage::instance();
            return $factory->createLocalDriver($config, 'cloud');
        },
    ],
];
