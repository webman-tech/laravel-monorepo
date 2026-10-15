<?php

return [
    'default' => 'local',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => runtime_path('storage'),
        ],
    ],
];
