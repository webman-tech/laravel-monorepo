<?php

namespace WebmanTech\LaravelTranslation\Facades;

use Illuminate\Contracts\Translation\Loader as LoaderContract;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator as LaravelTranslator;
use WebmanTech\LaravelTranslation\Helper\ConfigHelper;

/**
 * Laravel translation
 *
 * @method static string get($key, array $replace = [], $locale = null)
 * @method static string choice($key, $number, array $replace = [], $locale = null)
 * @method static string getLocale()
 * @method static void setLocale(string $locale)
 */
class Translator
{
    /**
     * @var null|TranslatorContract
     */
    protected static ?TranslatorContract $_instance = null;
    /**
     * @var bool
     */
    protected static bool $_shouldChangeLocale = false;

    public static function instance(): TranslatorContract
    {
        if (!static::$_instance) {
            static::$_instance = static::createTranslator();
            static::$_shouldChangeLocale = ConfigHelper::get('app.change_locale', class_exists(\Symfony\Component\Translation\Translator::class));
        }
        if (static::$_shouldChangeLocale) {
            static::$_instance->setLocale(locale());
        }
        return static::$_instance;
    }

    /**
     * https://github.com/laravel/framework/blob/11.x/src/Illuminate/Translation/TranslationServiceProvider.phphttps://github.com/laravel/framework/blob/11.x/src/Illuminate/Translation/TranslationServiceProvider.php
     * @return TranslatorContract
     */
    protected static function createTranslator(): TranslatorContract
    {
        // registerLoader
        $loader = static::createLoader();
        $locale = config('translation.locale', 'zh_CN');
        $translator = new LaravelTranslator($loader, $locale);
        if ($fallback = config('translation.fallback_locale', [])) {
            if (is_array($fallback)) {
                foreach ($fallback as $value) {
                    if ($value !== $locale) {
                        $fallback = $locale;
                        break;
                    }
                }
            }
            if (is_string($fallback)) {
                $translator->setFallback($fallback);
            }
        }
        return $translator;
    }

    protected static function createLoader(): LoaderContract
    {
        return new FileLoader(new Filesystem(), config('translation.path'));
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::instance()->{$name}(... $arguments);
    }
}
