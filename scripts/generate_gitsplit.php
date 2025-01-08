<?php
/**
 * 扫描 src 目录生成 gitsplit.yml
 */

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

require __DIR__ . '/../vendor/autoload.php';

$scanDir = __DIR__ . '/../src';
$files = new Filesystem();

$splits = collect($files->directories($scanDir))
    ->map(function ($dir) use ($scanDir) {
        return str_replace($scanDir . DIRECTORY_SEPARATOR, '', $dir);
    })
    ->map(function (string $name) {
        $projectName = 'webman-tech/' . Str::snake($name, '-');
        return <<<YML
  - prefix: "src/{$name}"
    target: "https://\${GH_TOKEN}@github.com/{$projectName}.git"
YML;
    })
    ->implode("\n");

$content = <<<YML
# https://github.com/jderusse/docker-gitsplit
cache_url: "cache/gitsplit"
splits:
{$splits}
origins:
  - ^\d+.x$
YML;

file_put_contents(__DIR__ . '/../.gitsplit.yml', $content);

echo "Done\n";
