<?php

use Symfony\Component\Process\Process;
use WebmanTech\LaravelConsole\Facades\Artisan;
use WebmanTech\LaravelConsole\Kernel;
use function WebmanTech\CommonUtils\base_path;

test('instance', function () {
    expect(Artisan::instance())->toBeInstanceOf(Kernel::class);
});

test('artisan', function () {
    // version、name 在配置中定义
    expect(doArtisan('--version'))->toContain('Webman Artisan Test 9.9.9');

    // list
    $listOutput = doArtisan('list');

    // 扫描 webman/console 的命令
    $this->assertStringContainsString('make:controller', $listOutput);
    $this->assertStringContainsString('plugin:create', $listOutput);

    // 扫描 illuminate/database 的命令
    $this->assertStringContainsString('migrate', $listOutput);
    $this->assertStringContainsString('migrate:rollback', $listOutput);
    $this->assertStringContainsString('make:migration', $listOutput);

    // 扫描 app/command 的命令
    $this->assertStringContainsString('sample:symfony', $listOutput);
    $this->assertStringContainsString('sample:laravel', $listOutput);

    // 扫描 plugin/ 的命令
    $this->assertStringContainsString('sample:tt:symfony', $listOutput);
});

test('command', function () {
    expect(doArtisan('sample:symfony'))->toContain('sample:symfony result');
    // 支持 symfony command
    expect(doArtisan('sample:laravel'))->toContain('sample:laravel result');
    // 支持 laravel command
});

test('artisan call', function () {
    expect(Artisan::call('sample:laravel'))->toEqual(0);
    expect(doArtisan('sample:laravel'))->toContain(trim(Artisan::output()));
});

function doArtisan(string $command): string
{
    //return Artisan::call($command);

    $phpBinary = PHP_BINARY;
    $process = Process::fromShellCommandline("$phpBinary artisan $command --no-ansi", base_path());
    $process->run();
    return trim($process->getOutput());
}
