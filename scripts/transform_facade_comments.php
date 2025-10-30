<?php

require_once __DIR__ . '/../vendor/autoload.php';

$configs = [
    \WebmanTech\LaravelDatabase\Facades\DB::class => [
        'from' => \Illuminate\Support\Facades\DB::class,
    ],
    \WebmanTech\LaravelConsole\Facades\Artisan::class => [
        'from' => \Illuminate\Support\Facades\Artisan::class,
        'change' => [
            'remove_fns' => [
                'queue', // 暂不支持
                'command', // ClosureCommand 没有，不支持
            ],
            'return' => [
                '\Illuminate\Foundation\Console\Kernel' => '\WebmanTech\LaravelConsole\Kernel',
            ],
        ],
        'add' => [
            ' *',
            ' * @see \WebmanTech\LaravelConsole\Kernel',
        ],
    ],
    \WebmanTech\LaravelHttp\Facades\Http::class => [
        'from' => \Illuminate\Support\Facades\Http::class,
        'add' => [
            ' *',
            ' * @see \Illuminate\Http\Client\Factory',
            ' * @see \Illuminate\Support\Facades\Http',
        ],
    ],
    \WebmanTech\LaravelHttp\Facades\LaravelRequest::class => [
        'from' => \Illuminate\Support\Facades\Request::class,
        'change' => [
            'remove_fns' => [
                'instance', // 已经在类中提供了
                // route 不支持
                'route',
                // session 不支持
                'session',
                'getSession',
                'setLaravelSession',
                'hasPreviousSession',
                'setSession',
                'hasSession',
                // URL 的，macro 未注入不支持
                'hasValidSignature',
            ],
        ],
        'add' => [
            ' *',
            ' * @see \Illuminate\Http\Request',
            ' * @method static \Illuminate\Http\Request createFromWebman(\Webman\Http\Request|null $request = null)',
        ],
    ],
    \WebmanTech\LaravelCache\Facades\Cache::class => [
        'from' => \Illuminate\Support\Facades\Cache::class,
        'add' => [
            ' *',
            ' * @see \Illuminate\Cache\CacheManager',
            ' * @see \Illuminate\Cache\Repository',
        ],
    ],
    \WebmanTech\LaravelCache\Facades\CacheRateLimiter::class => [
        'from' => \Illuminate\Support\Facades\RateLimiter::class,
        'add' => [
            ' *',
            ' * @see \Illuminate\Cache\RateLimiter',
        ],
    ],
    \WebmanTech\LaravelFilesystem\Facades\Storage::class => [
        'from' => \Illuminate\Support\Facades\Storage::class,
        'change' => [
            'return' => [
                '\Illuminate\Contracts\Filesystem\Filesystem' => '\Illuminate\Filesystem\FilesystemAdapter',
                '\Illuminate\Contracts\Filesystem\Cloud' => '\Illuminate\Filesystem\FilesystemAdapter',
                '\Symfony\Component\HttpFoundation\StreamedResponse' => '\Webman\Http\Response',
            ],
        ],
        'add' => [
            ' *',
            ' * @see \Illuminate\Support\Facades\Storage',
            ' * @see \Illuminate\Filesystem\FilesystemAdapter',
        ],
    ],
    \WebmanTech\LaravelFilesystem\Facades\File::class => [
        'from' => \Illuminate\Support\Facades\File::class,
        'add' => [
            ' *',
            ' * @see \Illuminate\Filesystem\Filesystem',
            ' * @see \Illuminate\Support\Facades\File',
        ]
    ],
    \WebmanTech\LaravelValidation\Facades\Validator::class => [
        'from' => \Illuminate\Support\Facades\Validator::class,
        'change' => [
            'remove_fns' => [
                'getTranslator', // 已经在类中提供了
            ],
        ],
        'add' => [
            ' *',
            ' * @see \Illuminate\Validation\Factory',
            ' * @see \Illuminate\Support\Facades\Validator',
        ],
    ],
    \WebmanTech\LaravelProcess\Facades\Process::class => [
        'from' => \Illuminate\Support\Facades\Process::class,
        'add' => [
            ' *',
            ' * @see \Illuminate\Process\Factory',
            ' * @see \Illuminate\Support\Facades\Process',
        ],
    ],
];

foreach ($configs as $targetClass => $config) {
    $methods = read_class_static_methods_from_comments($config['from']);
    $comments = [
        '/**'
    ];
    foreach ($methods as $info) {
        if (isset($config['change']['return'])) {
            $value = $info['return'];
            $info['return'] = $config['change']['return'][$value] ?? $value;
        }
        if (isset($config['change']['remove_fns']) && in_array($info['function'], $config['change']['remove_fns'], true)) {
            continue;
        }
        $comments[] = " * @method static {$info['return']} {$info['function_full']}";
    }

    $comments = array_merge($comments, $config['add'] ?? []);
    $comments[] = ' */';

    write_comments_to_class($targetClass, implode("\n", $comments));
    echo "Write comments to $targetClass\n";
}

function read_class_static_methods_from_comments(string $className): array
{
    $reflection = new ReflectionClass($className);
    $comments = $reflection->getDocComment();
    return collect(explode("\n", $comments))
        ->map(function ($comment) {
            $comment = trim($comment, "/* \t\n\r");
            if ($comment && str_starts_with($comment, '@method static')) {
                $comment = str_replace('@method static ', '', $comment);
                $return = explode(' ', $comment)[0];
                $functionFull = substr($comment, strlen($return) + 1);
                $function = explode('(', $functionFull)[0];
                return [
                    'return' => $return,
                    'function' => $function,
                    'function_full' => $functionFull,
                ];
            }
            return $comment;
        })
        ->filter(fn($comment) => is_array($comment))
        ->toArray();
}

function write_comments_to_class(string $className, string $comments): void
{
    $reflection = new ReflectionClass($className);
    $file = $reflection->getFileName();
    $oldComments = $reflection->getDocComment();
    $content = file_get_contents($file);
    $content = str_replace($oldComments, $comments, $content);
    file_put_contents($file, $content);
}
