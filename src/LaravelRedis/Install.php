<?php
namespace WebmanTech\LaravelRedis;

class Install
{
    const WEBMAN_PLUGIN = true;

    /**
     * Install
     * @return void
     */
    public static function install()
    {
        \Webman\Redis\Install::install();
    }

    /**
     * Uninstall
     * @return void
     */
    public static function uninstall()
    {
        \Webman\Redis\Install::uninstall();
    }
}
