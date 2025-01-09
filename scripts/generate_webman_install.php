<?php
/**
 * 自动生成全局的 webman Install.php
 */

use Symfony\Component\Finder\Finder;

require __DIR__ . '/../vendor/autoload.php';

$composerFile = __DIR__ . '/../composer.json';
$finder = Finder::create()
    ->in(__DIR__ . '/../src')
    ->depth(1)
    ->name('Install.php');

$install = [];
$uninstall = [];
foreach ($finder as $file) {
    $install[] = '        \\WebmanTech\\' . $file->getRelativePath() . '\\Install::install();';
    $uninstall[] = '        \\WebmanTech\\' . $file->getRelativePath() . '\\Install::uninstall();';
}
$install = implode("\n", $install);
$uninstall = implode("\n", $uninstall);

$content = <<<PHP
<?php
namespace WebmanTech;

class Install
{
    const WEBMAN_PLUGIN = true;

    /**
     * Install
     * @return void
     */
    public static function install()
    {
{$install}
    }

    /**
     * Uninstall
     * @return void
     */
    public static function uninstall()
    {
{$uninstall}
    }
}

PHP;

file_put_contents(__DIR__ . '/../src/Install.php', $content);
echo "Done\n";
