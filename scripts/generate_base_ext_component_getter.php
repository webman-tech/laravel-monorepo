<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/utils.php';

foreach (get_packages() as $package) {
    $filename = path_join($package['dir_path'], 'Helper/BaseExtComponentGetter.php');
    if (!file_exists($filename)) {
        continue;
    }
    $content = strtr(file_get_contents(__DIR__ . '/stubs/BaseExtComponentGetter.stub'), [
        '{namespace}' => $package['class_namespace'] . '\\Helper',
    ]);
    write_file($filename, $content, true);
}
