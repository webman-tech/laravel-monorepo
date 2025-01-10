<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

// pest()->extend(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

function get_misc_path(string $name): string
{
    return __DIR__ . "/Fixtures/misc/{$name}";
}

function get_misc_content(string $name): string
{
    $content = file_get_contents(get_misc_path($name));

    if (str_starts_with($name, 'http/')) {
        $content = str_replace("\n", "\r\n", $content);
        $content = str_replace("\r\n\n", "\r\n", $content);
        if (!str_contains($content, "\r\n\r\n")) {
            $content .= "\r\n";
        } else {
            $content = rtrim($content);
        }
    }

    return $content;
}

function php84_error_reporting_change(): void
{
    if (!version_compare(PHP_VERSION, '8.4.0', '>=')) {
        return;
    }
    \Tests\TestCase::$context['errorReporting'] = error_reporting();
    error_reporting(E_ALL & ~E_DEPRECATED);
}

function php84_error_reporting_reset(): void
{
    if (!version_compare(PHP_VERSION, '8.4.0', '>=')) {
        return;
    }
    error_reporting(\Tests\TestCase::$context['errorReporting']);
}
