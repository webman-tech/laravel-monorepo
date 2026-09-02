<?php

use Symfony\Component\Process\Process;

// ============================================================
// webman CLI 命令测试（webmanCommand 运行 php webman <args>）
// webman CLI 只加载 app/command/ 和 plugin/*/command/ 下的 Symfony 命令
// ============================================================

test('webman command list', function () {
    $result = $this->webmanCommand('list');
    $result->assertOk();

    // webman 内置命令
    $result->assertOutputContains('make:controller');
    $result->assertOutputContains('plugin:create');
});

test('webman list shows app symfony commands', function () {
    $result = $this->webmanCommand('list');
    $result->assertOk();

    // app/command/ 下的 Symfony 命令
    $result->assertOutputContains('sample:symfony');
});

test('webman run symfony command', function () {
    $this->webmanCommand('sample:symfony')
        ->assertOk()
        ->expectsOutput('sample:symfony result');
});

// ============================================================
// artisan 命令测试（php artisan 运行 Laravel 命令）
// webmanCommand() 固定使用 webman 入口，artisan 需用 Process 直接调用
// ============================================================

function artisanCommand(string ...$args): \WebmanTech\Testing\Console\CommandResult
{
    $config = \WebmanTech\Testing\Server::instance()->config();
    $process = new Process(
        array_merge([$config->phpBinary, 'artisan', ...$args]),
        $config->appDir,
        null,
        null,
        $config->processTimeout,
    );
    $process->run();

    return new \WebmanTech\Testing\Console\CommandResult($process);
}

test('artisan list shows laravel commands', function () {
    $result = artisanCommand('list');
    $result->assertOk();

    // Laravel 内置命令
    $result->assertOutputContains('migrate');
});

test('artisan run laravel command', function () {
    artisanCommand('sample:laravel')
        ->assertOk()
        ->expectsOutput('sample:laravel result');
});
