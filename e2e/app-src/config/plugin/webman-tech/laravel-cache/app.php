<?php

use Illuminate\Cache\FileStore;
use Illuminate\Cache\NullStore;

return [
    'enable' => true,
    'flush' => [
        'prevent' => true,
        'ignore_store' => [FileStore::class, NullStore::class],
    ],
];
