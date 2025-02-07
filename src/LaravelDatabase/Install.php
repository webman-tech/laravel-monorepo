<?php
namespace WebmanTech\LaravelDatabase;

class Install
{
    const WEBMAN_PLUGIN = true;

    /**
     * Install
     * @return void
     */
    public static function install()
    {
        \Webman\Database\Install::install();
    }

    /**
     * Uninstall
     * @return void
     */
    public static function uninstall()
    {
        \Webman\Database\Install::uninstall();
    }
}
