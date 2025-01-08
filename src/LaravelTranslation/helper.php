<?php

use WebmanTech\LaravelTranslation\Facades\Translator;

if (!function_exists('transL')) {
    /**
     * Translate the given message.
     * 由于 webman 下默认使用 symfony/translation，且已经定义过 trans 方法，为了不冲突，此处使用 transL
     *
     * @param string|null $key
     * @param array $replace
     * @param string|null $locale
     * @return ($key is null ? \Illuminate\Contracts\Translation\Translator : array|string)
     */
    function transL($key = null, $replace = [], $locale = null)
    {
        if (is_null($key)) {
            return Translator::instance();
        }

        return Translator::get($key, $replace, $locale);
    }
}

if (!function_exists('trans_choice')) {
    /**
     * Translates the given message based on a count.
     *
     * @param string $key
     * @param \Countable|int|array $number
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    function trans_choice($key, $number, array $replace = [], $locale = null)
    {
        return Translator::choice($key, $number, $replace, $locale);
    }
}

if (!function_exists('__')) {
    /**
     * Translate the given message.
     *
     * @param string|null $key
     * @param array $replace
     * @param string|null $locale
     * @return string|array|null
     */
    function __($key = null, $replace = [], $locale = null)
    {
        if (is_null($key)) {
            return $key;
        }

        return transL($key, $replace, $locale);
    }
}
