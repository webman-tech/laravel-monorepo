<?php
/**
 * 自动生成全局的 webman Install.php
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/utils.php';

$install = [];
$uninstall = [];
get_packages()
    ->each(function ($package) use(&$install, &$uninstall) {
        if (!file_exists($package['dir_path'] . '/Install.php')) {
            return;
        }
        $install[] = '        \\WebmanTech\\' . $package['dir_name'] . '\\Install::install();';
        $uninstall[] = '        \\WebmanTech\\' . $package['dir_name'] . '\\Install::uninstall();';
    });
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

write_file('src/Install.php', $content);
