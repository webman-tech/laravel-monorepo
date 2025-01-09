<?php

namespace WebmanTech\LaravelHttp\Facades;

use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Http\UploadedFile as IlluminateUploadedFile;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use Webman\Http\UploadFile as WebmanUploadedFile;
use WebmanTech\LaravelFilesystem\Facades\Storage;
use WebmanTech\LaravelHttp\Helper\ExtComponentGetter;

class LaravelUploadedFile extends IlluminateUploadedFile
{
    public static function wrapper(WebmanUploadedFile|IlluminateUploadedFile|SymfonyUploadedFile $file, bool $test = false): static
    {
        if ($file instanceof WebmanUploadedFile) {
            $self = static::createFromWebman($file);
        } elseif ($file instanceof IlluminateUploadedFile) {
            $self = new static(
                $file->getRealPath(),
                $file->getClientOriginalName(),
                $file->getClientMimeType(),
                $file->getError(),
                $test,
            );
        } elseif ($file instanceof SymfonyUploadedFile) {
            $self = static::createFromBase($file, $test);
        } else {
            throw new \InvalidArgumentException('Unsupported file type');
        }

        return $self->withOriginFile($file);
    }

    public static function createFromWebman(WebmanUploadedFile $file): static
    {
        $self = new static(
            $file->getRealPath() ?: '',
            $file->getUploadName() ?? '',
            $file->getUploadMimeType(),
            $file->getUploadErrorCode(),
            false
        );
        $self->withOriginFile($file);
        return $self;
    }

    protected $_originFile;

    protected function withOriginFile($file): static
    {
        $this->_originFile = $file;
        return $this;
    }

    /**
     * webman 下不能使用 is_uploaded_file 校验
     * @inheritDoc
     */
    public function isValid(): bool
    {
        return \UPLOAD_ERR_OK === $this->getError();
    }

    /**
     * 使用 webman 的文件移动功能
     * @inheritDoc
     */
    public function move(string $directory, string $name = null): File
    {
        $file = $this->_originFile instanceof WebmanUploadedFile
            ? $this->_originFile
            : new WebmanUploadedFile($this->getRealPath(), $this->getClientOriginalName(), $this->getClientMimeType(), $this->getError());
        $name = $name ?: $this->getClientOriginalName();

        $file = $file->move(rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $name);
        return new File(
            $file->getRealPath(),
            false
        );
    }

    /**
     * @inheritDoc
     * @see https://www.workerman.net/doc/webman/request.html#%E8%8E%B7%E5%8F%96%E4%B8%8A%E4%BC%A0%E6%96%87%E4%BB%B6
     */
    public static function getMaxFilesize(): int|float
    {
        return config('server.max_package_size', 10 * 1024 * 1024);
    }

    /**
     * 移除对 illuminate/container 的依赖
     * @inheritDoc
     */
    public function storeAs($path, $name = null, $options = [])
    {
        if (is_null($name) || is_array($name)) {
            [$path, $name, $options] = ['', $path, $name ?? []];
        }

        $options = $this->parseOptions($options);

        $disk = Arr::pull($options, 'disk');

        $storage = ExtComponentGetter::get(FilesystemFactory::class, [Storage::class, fn() => Storage::instance()]);

        return $storage->disk($disk)->putFileAs(
            $path, $this, $name, $options
        );
    }
}
