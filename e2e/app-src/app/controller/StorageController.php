<?php

namespace app\controller;

use support\Request;
use WebmanTech\LaravelFilesystem\Facades\File;
use WebmanTech\LaravelFilesystem\Facades\Storage;
use WebmanTech\LaravelFilesystem\FilesystemManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Webman\Http\Response;

class StorageController
{
    public function index(Request $request): \support\Response
    {
        $testPath = normalize_path(storage_path('test-storage'));
        @mkdir($testPath);

        $results = [];

        // instance
        $results['instance'] = Storage::instance() instanceof FilesystemManager;

        // filesystem manager function
        $results['disk_is_filesystem'] = Storage::disk() instanceof FilesystemContract;
        $results['disk_is_adapter'] = Storage::disk() instanceof FilesystemAdapter;
        $publicDisk = Storage::disk('public');
        $results['public_disk_is_adapter'] = $publicDisk instanceof FilesystemAdapter;
        $results['cloud_is_cloud'] = Storage::cloud() instanceof Cloud;

        // build
        $results['build'] = Storage::build(storage_path() . '/app') instanceof FilesystemContract;

        // exists/missing
        $results['exists_false'] = Storage::exists('not_exist.txt') === false;
        $results['missing_true'] = Storage::missing('not_exist.txt') === true;

        // put/get
        Storage::put('test.txt', 'hello');
        $results['get'] = Storage::get('test.txt') === 'hello';
        $results['exists_true'] = Storage::exists('test.txt') === true;

        // response - webman Response
        $storageResponse = Storage::response('test.txt');
        $results['response_is_webman'] = $storageResponse instanceof Response;
        $results['response_body'] = $storageResponse->rawBody() === 'hello';

        // download - webman Response
        $downloadResponse = Storage::download('test.txt');
        $results['download_is_webman'] = $downloadResponse instanceof Response;
        $results['download_header'] = str_contains($downloadResponse->getHeader('Content-Disposition'), 'attachment');

        // delete
        $results['delete'] = Storage::delete('test.txt') === true;

        // cleanup
        File::deleteDirectory($testPath);

        return json($results);
    }

    public function extend(Request $request): \support\Response
    {
        $results = [];

        $map = array_filter([
            'qiniu' => \Overtrue\Flysystem\Qiniu\QiniuAdapter::class,
            'cos' => \Overtrue\Flysystem\Cos\CosAdapter::class,
            'oss' => \AlphaSnow\Flysystem\Aliyun\AliyunAdapter::class,
        ]);

        foreach ($map as $name => $instance) {
            $disk = Storage::disk($name);
            $results[$name . '_is_adapter'] = $disk instanceof FilesystemAdapter;
            $results[$name . '_adapter_type'] = $disk->getAdapter() instanceof $instance;
        }

        return json($results);
    }
}

function normalize_path(string $path): string
{
    return str_replace('\\', '/', $path);
}
