<?php

return [
    'httpClient' => [
        'timeout' => 5,
        'connect_timeout' => 1,
    ],
    // 将当前环境变量传递给 server 子进程（含 APP_PORT 等），确保子进程能正常启动
    // getenv() 返回所有环境变量，覆盖 APP_PORT 确保与测试进程一致
    'serverEnv' => array_merge(getenv() ?: [], [
        'APP_PORT' => (string) (getenv('APP_PORT') ?: 8787),
    ]),
];
