<?php

use Illuminate\Http\UploadedFile as IlluminateUploadedFile;
use Webman\Http\UploadFile as WebmanUploadedFile;
use WebmanTech\LaravelHttp\Facades\LaravelUploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

test('wrap', function () {
    $file = new WebmanUploadedFile(get_misc_path('http/get1.txt'), 'get1.txt', 'text/plain', 0);

    $laravelFile = LaravelUploadedFile::createFromWebman($file);
    expect($laravelFile)->toBeInstanceOf(IlluminateUploadedFile::class)
        ->and($laravelFile->getClientOriginalName())->toBe('get1.txt');

    $symfonyFile = LaravelUploadedFile::createForSymfonyFromWebman($file);
    expect($symfonyFile)->toBeInstanceOf(SymfonyUploadedFile::class)
        ->and($symfonyFile->getClientOriginalName())->toBe('get1.txt');

    $laravelFile = LaravelUploadedFile::wrapper($file);
    expect($laravelFile)->toBeInstanceOf(IlluminateUploadedFile::class)
        ->and($laravelFile->getClientOriginalName())->toBe('get1.txt');

    $laravelFile = LaravelUploadedFile::wrapper($symfonyFile);
    expect($laravelFile)->toBeInstanceOf(IlluminateUploadedFile::class)
        ->and($laravelFile->getClientOriginalName())->toBe('get1.txt');

    $laravelFile = LaravelUploadedFile::wrapper($laravelFile);
    expect($laravelFile)->toBeInstanceOf(IlluminateUploadedFile::class)
        ->and($laravelFile->getClientOriginalName())->toBe('get1.txt');
});
