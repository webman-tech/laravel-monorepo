<?php
/**
 * 升级 laravel 的包，例如从 11.x => 12.x
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/utils.php';

[, $fromVersion, $toVersion] = $argv;
if (!is_numeric($fromVersion) || !is_numeric($toVersion)) {
    write_log('Usage: php scripts/upgrade_laravel_version.php 11 12');
    exit(1);
}

// 修改 composer.json 的依赖
function update_composer_json(string $composerFile)
{
    global $fromVersion, $toVersion;
    $content = strtr(file_get_contents($composerFile), [
        '^' . $fromVersion . '.0' => '^' . $toVersion . '.0',
    ]);
    write_file($composerFile, $content, true);
}

function update_dot_x(string $file)
{
    global $fromVersion, $toVersion;
    $content = strtr(file_get_contents($file), [
        $fromVersion . '.x' => '' . $toVersion . '.x',
    ]);
    write_file($file, $content, true);
}

// 更新 composer.json
update_composer_json(root_path('composer.json'));
foreach (get_packages() as $package) {
    $composerFile = path_join($package['dir_path'], 'composer.json');
    if (!file_exists($composerFile)) {
        continue;
    }
    update_composer_json($composerFile);
}

// 更新 dot_x
update_dot_x(root_path('.github/workflows/split.yml'));
update_dot_x(root_path('scripts/replace_validation_files.php'));
