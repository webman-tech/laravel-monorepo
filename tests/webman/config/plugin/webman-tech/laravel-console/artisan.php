<?php

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;

return [
    /**
     * @see \WebmanTech\LaravelConsole\Kernel::$config
     */
    'version' => '9.9.9',
    'name' => 'Webman Artisan Test',
    'container' => function (): ContainerContract {
        $container = Container::getInstance();
        // add some deps
        return $container;
    },
    'commands' => [
        // commandName
    ],
    'commands_path' => [
    ],
];
