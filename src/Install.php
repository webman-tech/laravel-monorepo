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
        \WebmanTech\LaravelCache\Install::install();
        \WebmanTech\LaravelConsole\Install::install();
        \WebmanTech\LaravelDatabase\Install::install();
        \WebmanTech\LaravelFilesystem\Install::install();
        \WebmanTech\LaravelHttp\Install::install();
        \WebmanTech\LaravelRedis\Install::install();
        \WebmanTech\LaravelTranslation\Install::install();
        \WebmanTech\LaravelValidation\Install::install();
    }

    /**
     * Uninstall
     * @return void
     */
    public static function uninstall()
    {
        \WebmanTech\LaravelCache\Install::uninstall();
        \WebmanTech\LaravelConsole\Install::uninstall();
        \WebmanTech\LaravelDatabase\Install::uninstall();
        \WebmanTech\LaravelFilesystem\Install::uninstall();
        \WebmanTech\LaravelHttp\Install::uninstall();
        \WebmanTech\LaravelRedis\Install::uninstall();
        \WebmanTech\LaravelTranslation\Install::uninstall();
        \WebmanTech\LaravelValidation\Install::uninstall();
    }
}
