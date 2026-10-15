<?php

declare(strict_types=1);

/**
 * 非 webman 宿主初始化示例：经 RuntimeCustomRegister 注册运行时能力 + 宿主容器绑定 db 等组件。
 */

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use WebmanTech\CommonUtils\Constants;
use WebmanTech\CommonUtils\Runtime;
use WebmanTech\CommonUtils\RuntimeCustomRegister;
use WebmanTech\CommonUtils\Timer\PcntlTimer;

require_once __DIR__ . '/vendor/autoload.php';

// 固定为自定义运行时（宿主既非 webman 也非 laravel）
Runtime::changeRuntime(Constants::RUNTIME_CUSTOM);

$basePath = __DIR__;
$configDir = $basePath . '/config';
$locale = 'zh_CN';

$pathResolver = fn(string $base, ?string $path): string => ($path === null || $path === '') ? $base : $base . '/' . ltrim($path, '/');

$config = new Repository();
$container = new Container();
// 与 Illuminate\Container\Container::getInstance() 保持一致，方便使用 illuminate 静态门面的组件
Container::setInstance($container);

// 运行时能力注册（供 webman-tech 系组件使用；先于配置加载，配置文件内会调用全局函数）
RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_BASE_PATH, fn(?string $path = null) => $pathResolver($basePath, $path));
RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_RUNTIME_PATH, fn(?string $path = null) => $pathResolver($basePath . '/runtime', $path));
RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_CONFIG_PATH, fn(?string $path = null) => $pathResolver($configDir, $path));
RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_APP_PATH, fn(?string $path = null) => $pathResolver($basePath . '/app', $path));
RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_VENDOR_PATH, fn(?string $path = null) => $pathResolver($basePath . '/vendor', $path));
RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_CONFIG_GET, fn(string $key, mixed $default = null) => $config->get($key, $default));
RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_CONTAINER, fn() => $container);
RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_LOG_CHANNEL, fn(?string $name = null): LoggerInterface => (new Logger($name ?? 'plain'))->pushHandler(new NullHandler()));
RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_LANG_GET_LOCALE, function () use (&$locale): string {
    return $locale;
});
RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_LANG_SET_LOCALE, function (string $newLocale) use (&$locale) {
    $locale = $newLocale;
});
RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_REQUEST, fn() => SymfonyRequest::createFromGlobals());
RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_RESPONSE, fn() => new SymfonyResponse());
RuntimeCustomRegister::register(RuntimeCustomRegister::KEY_TIMER, PcntlTimer::call(...));

// 按 webman 的配置约定加载：config/a/b.php => 配置键 a.b
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($configDir, FilesystemIterator::SKIP_DOTS)
);
foreach ($iterator as $file) {
    /** @var \SplFileInfo $file */
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $relativePath = substr((string)$file, strlen($configDir) + 1, -4);
    $key = implode('.', explode('/', $relativePath));
    $config->set($key, require (string)$file);
}

// 宿主自持 db（sqlite 内存）：容器优先解析，各组件包不再回退 webman 的 support\Db
$container->instance('config', $config);
$container->singleton(ConnectionResolverInterface::class, function (Container $app) {
    return new DatabaseManager($app, new ConnectionFactory($app));
});
