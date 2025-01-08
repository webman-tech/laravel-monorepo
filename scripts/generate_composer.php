<?php
/**
 * 将子包的 composer 相关依赖汇总到最外层
 */

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

require __DIR__ . '/../vendor/autoload.php';

$composerFile = __DIR__ . '/../composer.json';
$finder = Finder::create()
    ->in(__DIR__ . '/../src')
    ->depth(1)
    ->name('composer.json');

$require = collect();
$comments = collect();
$replace = [];
$autoloadFiles = collect();
foreach ($finder as $file) {
    $json = json_decode(file_get_contents($file->getRealPath()), true);
    $require = $require->merge($json['require'] ?? []);
    $comments = $comments->merge($json['_comment'] ?? []);
    $replace[$json['name']] = 'self.version';
    if ($files = $json['autoload']['files'] ?? []) {
        $autoloadFiles = $autoloadFiles->merge(
            array_map(fn($fileName) => 'src/' . $file->getRelativePath() . '/' . $fileName, $files)
        );
    }
}

$json = json_decode(file_get_contents($composerFile), true);
$json['require'] = $require->toArray();
$json['_comment'] = $comments->unique()->values()->toArray();
$json['replace'] = $replace;
$json['autoload']['files'] = $autoloadFiles->toArray();

$content = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

file_put_contents($composerFile, $content);
echo "Write to $composerFile\n";

echo "Normalize composer.json\n";
$process = Process::fromShellCommandline('composer normalize --no-check-lock --no-update-lock --indent-size=2 --indent-style=space', __DIR__ . '/../');
if ($code = $process->run()) {
    echo "Failed with code: {$code}\n";
    echo $process->getErrorOutput();
    exit($code);
}
echo "Done\n";
