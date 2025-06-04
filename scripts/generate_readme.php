<?php
/**
 * 生成 README 下的组件列表
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/utils.php';

$listContent = get_packages()
    ->map(function ($package) {
        return "- [{$package['composer_name']}](./src/{$package['dir_name']}/README.md)";
    })
    ->implode("\n");

$readmeFile = root_path('README.md');
$content = file_get_contents($readmeFile);

$pattern = '/<!-- packages:start -->.*<!-- packages:end -->/s';
$replacement = '<!-- packages:start -->' . PHP_EOL . PHP_EOL . $listContent . PHP_EOL . PHP_EOL . '<!-- packages:end -->';
$content = preg_replace($pattern, $replacement, $content);

write_file($readmeFile, $content, true);
