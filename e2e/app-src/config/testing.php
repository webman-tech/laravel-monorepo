<?php

return [
    'httpClient' => [
        'timeout' => 5,
        'connect_timeout' => 1,
        // 测试请求直连 127.0.0.1，显式禁用代理：libcurl 会自动读取 http_proxy 环境变量，
        // 透明代理转发会破坏 keep-alive 连接复用（同一连接的第二个请求被服务端判 400）
        'curl' => [
            CURLOPT_PROXY => '',
            CURLOPT_NOPROXY => '*',
        ],
    ],
    // 将当前环境变量传递给 server 子进程（含 APP_PORT 等），确保子进程能正常启动
    // getenv() 返回所有环境变量，覆盖 APP_PORT 确保与测试进程一致
    'serverEnv' => array_merge(getenv() ?: [], [
        'APP_PORT' => (string) (getenv('APP_PORT') ?: 8787),
    ]),
];
