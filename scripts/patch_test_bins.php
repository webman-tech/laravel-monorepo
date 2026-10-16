<?php

/**
 * 给 vendor 下的 pest/phpunit 真实 bin 前置 BASE_PATH 定义（幂等）
 *
 * 背景：webman-framework 的 helpers.php 挂在 composer files autoload 上，BASE_PATH 常量在
 * autoload 时从 cwd 固化为项目根；而测试的 webman 夹具应用在 tests/webman，必须在 autoload
 * 之前定义 BASE_PATH 指向夹具，框架的 support/bootstrap.php 才能原生加载配置。
 *
 * PhpStorm 等外部工具会直接执行 vendor/bin/pest（无法经过仓库内的入口脚本），因此通过
 * post-autoload-dump 在 composer install/update/dump-autoload 后自动补丁真实 bin（bin 被
 * composer 重装后也会自动重新打上），保证任意入口下 base_path() 均指向夹具应用。
 */

$root = dirname(__DIR__);

$targets = [
    // 相对路径 => 从 bin 所在目录到项目根的 dirname 层级
    'vendor/pestphp/pest/bin/pest' => 4,
    'vendor/phpunit/phpunit/phpunit' => 3,
];

$patched = 0;
foreach ($targets as $relativePath => $depth) {
    $file = $root . '/' . $relativePath;
    if (!is_file($file)) {
        continue; // 组件未安装（如 --no-dev 安装）
    }

    $content = file_get_contents($file);
    if (str_contains($content, "define('BASE_PATH'")) {
        continue; // 已打补丁，幂等跳过
    }

    $code = "\n// @test-bootstrap: BASE_PATH 指向 tests/webman 夹具应用（由 scripts/patch_test_bins.php 自动维护，勿手动编辑）\n"
        . "if (!defined('BASE_PATH')) {\n"
        . "    define('BASE_PATH', dirname(__DIR__, $depth) . '/tests/webman');\n"
        . "}\n";

    // declare(strict_types=1) 必须是脚本第一条语句，插到它之后（无 declare 时插到 <?php 之后）
    $anchor = 'declare(strict_types=1);';
    $pos = strpos($content, $anchor);
    if ($pos !== false) {
        $insertAt = $pos + strlen($anchor);
    } else {
        $pos = strpos($content, '<?php');
        if ($pos === false) {
            continue;
        }
        $insertAt = $pos + strlen('<?php');
    }

    file_put_contents($file, substr_replace($content, $code, $insertAt, 0));
    $patched++;
}

if ($patched > 0) {
    echo "patched test bins (BASE_PATH -> tests/webman): $patched\n";
}
