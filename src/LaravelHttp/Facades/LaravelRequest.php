<?php

namespace WebmanTech\LaravelHttp\Facades;

use Closure;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Http\Request as BaseRequest;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Webman\Http\Request as WebmanRequest;
use Webman\Http\UploadFile as WebmanUploadFile;
use WebmanTech\CommonUtils\Request as UtilsRequest;
use WebmanTech\LaravelHttp\Helper\ConfigHelper;
use WebmanTech\LaravelHttp\Helper\ExtComponentGetter;
use WebmanTech\LaravelHttp\Mock\Request as IlluminateRequest;

/**
 * @method static string method()
 * @method static \Illuminate\Support\Uri uri()
 * @method static string root()
 * @method static string url()
 * @method static string fullUrl()
 * @method static string fullUrlWithQuery(array $query)
 * @method static string fullUrlWithoutQuery(array|string $keys)
 * @method static string path()
 * @method static string decodedPath()
 * @method static string|null segment(int $index, string|null $default = null)
 * @method static array segments()
 * @method static bool is(mixed ...$patterns)
 * @method static bool routeIs(mixed ...$patterns)
 * @method static bool fullUrlIs(mixed ...$patterns)
 * @method static string host()
 * @method static string httpHost()
 * @method static string schemeAndHttpHost()
 * @method static bool ajax()
 * @method static bool pjax()
 * @method static bool prefetch()
 * @method static bool secure()
 * @method static string|null ip()
 * @method static array ips()
 * @method static string|null userAgent()
 * @method static array getAcceptableContentTypes()
 * @method static \Illuminate\Http\Request merge(array $input)
 * @method static \Illuminate\Http\Request mergeIfMissing(array $input)
 * @method static \Illuminate\Http\Request replace(array $input)
 * @method static \Symfony\Component\HttpFoundation\InputBag|mixed json(string|null $key = null, mixed $default = null)
 * @method static \Illuminate\Http\Request createFrom(\Illuminate\Http\Request $from, \Illuminate\Http\Request|null $to = null)
 * @method static \Illuminate\Http\Request createFromBase(\Symfony\Component\HttpFoundation\Request $request)
 * @method static \Illuminate\Http\Request duplicate(array|null $query = null, array|null $request = null, array|null $attributes = null, array|null $cookies = null, array|null $files = null, array|null $server = null)
 * @method static void setRequestLocale(string $locale)
 * @method static void setDefaultRequestLocale(string $locale)
 * @method static mixed user(string|null $guard = null)
 * @method static string fingerprint()
 * @method static \Illuminate\Http\Request setJson(\Symfony\Component\HttpFoundation\InputBag $json)
 * @method static \Closure getUserResolver()
 * @method static \Illuminate\Http\Request setUserResolver(\Closure $callback)
 * @method static \Closure getRouteResolver()
 * @method static \Illuminate\Http\Request setRouteResolver(\Closure $callback)
 * @method static array toArray()
 * @method static void initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], string|resource|null $content = null)
 * @method static \Illuminate\Http\Request create(string $uri, string $method = 'GET', array $parameters = [], array $cookies = [], array $files = [], array $server = [], string|resource|null $content = null)
 * @method static void setFactory(callable|null $callable)
 * @method static void overrideGlobals()
 * @method static void setTrustedProxies(array $proxies, int $trustedHeaderSet)
 * @method static string[] getTrustedProxies()
 * @method static int getTrustedHeaderSet()
 * @method static void setTrustedHosts(array $hostPatterns)
 * @method static string[] getTrustedHosts()
 * @method static string normalizeQueryString(string|null $qs)
 * @method static void enableHttpMethodParameterOverride()
 * @method static bool getHttpMethodParameterOverride()
 * @method static void setAllowedHttpMethodOverride(string[]|null $methods)
 * @method static string[]|null getAllowedHttpMethodOverride()
 * @method static array getClientIps()
 * @method static string|null getClientIp()
 * @method static string getScriptName()
 * @method static string getPathInfo()
 * @method static string getBasePath()
 * @method static string getBaseUrl()
 * @method static string getScheme()
 * @method static int|string|null getPort()
 * @method static string|null getUser()
 * @method static string|null getPassword()
 * @method static string|null getUserInfo()
 * @method static string getHttpHost()
 * @method static string getRequestUri()
 * @method static string getSchemeAndHttpHost()
 * @method static string getUri()
 * @method static string getUriForPath(string $path)
 * @method static string getRelativeUriForPath(string $path)
 * @method static string|null getQueryString()
 * @method static bool isSecure()
 * @method static string getHost()
 * @method static void setMethod(string $method)
 * @method static string getMethod()
 * @method static string getRealMethod()
 * @method static string|null getMimeType(string $format)
 * @method static string[] getMimeTypes(string $format)
 * @method static string|null getFormat(string|null $mimeType, bool $subtypeFallback = null)
 * @method static void setFormat(string $format, string|string[] $mimeTypes)
 * @method static string|null getRequestFormat(string|null $default = 'html')
 * @method static void setRequestFormat(string|null $format)
 * @method static string|null getContentTypeFormat()
 * @method static void setDefaultLocale(string $locale)
 * @method static string getDefaultLocale()
 * @method static void setLocale(string $locale)
 * @method static string getLocale()
 * @method static bool isMethod(string $method)
 * @method static bool isMethodSafe()
 * @method static bool isMethodIdempotent()
 * @method static bool isMethodCacheable()
 * @method static string|null getProtocolVersion()
 * @method static string|resource getContent(bool $asResource = false)
 * @method static \Symfony\Component\HttpFoundation\InputBag getPayload()
 * @method static array getETags()
 * @method static bool isNoCache()
 * @method static string|null getPreferredFormat(string|null $default = 'html')
 * @method static string|null getPreferredLanguage(string[] $locales = null)
 * @method static string[] getLanguages()
 * @method static string[] getCharsets()
 * @method static string[] getEncodings()
 * @method static bool isXmlHttpRequest()
 * @method static bool preferSafeContent()
 * @method static bool isFromTrustedProxy()
 * @method static array filterPrecognitiveRules(array $rules)
 * @method static bool isAttemptingPrecognition()
 * @method static bool isPrecognitive()
 * @method static bool isJson()
 * @method static bool expectsJson()
 * @method static bool wantsJson()
 * @method static bool wantsMarkdown()
 * @method static bool accepts(string|array $contentTypes)
 * @method static string|null prefers(string|array $contentTypes)
 * @method static bool acceptsAnyContentType()
 * @method static bool acceptsJson()
 * @method static bool acceptsMarkdown()
 * @method static bool acceptsHtml()
 * @method static bool matchesType(string $actual, string $type)
 * @method static string format(string $default = 'html')
 * @method static string|array|null old(string|null $key = null, \Illuminate\Database\Eloquent\Model|string|array|null $default = null)
 * @method static void flash()
 * @method static void flashOnly(mixed $keys)
 * @method static void flashExcept(mixed $keys)
 * @method static void flush()
 * @method static string|array|null server(string|null $key = null, string|array|null $default = null)
 * @method static bool hasHeader(string $key)
 * @method static string|array|null header(string|null $key = null, string|array|null $default = null)
 * @method static string|null bearerToken()
 * @method static array keys()
 * @method static array all(mixed $keys = null)
 * @method static mixed input(string|null $key = null, mixed $default = null)
 * @method static \Illuminate\Support\Fluent fluent(array|string|null $key = null, array $default = [])
 * @method static string|array|null query(string|null $key = null, string|array|null $default = null)
 * @method static string|array|null post(string|null $key = null, string|array|null $default = null)
 * @method static bool hasCookie(string $key)
 * @method static string|array|null cookie(string|null $key = null, string|array|null $default = null)
 * @method static array allFiles()
 * @method static bool hasFile(string $key)
 * @method static array|\Illuminate\Http\UploadedFile|\Illuminate\Http\UploadedFile[]|null file(string|null $key = null, mixed $default = null)
 * @method static \Illuminate\Http\Request dump(mixed $keys = [])
 * @method static never dd(mixed ...$args)
 * @method static bool exists(string|array $key)
 * @method static bool has(string|array $key)
 * @method static bool hasAny(string|array $keys)
 * @method static \Illuminate\Http\Request|mixed whenHas(string $key, callable $callback, callable|null $default = null)
 * @method static bool filled(string|array $key)
 * @method static bool isNotFilled(string|array $key)
 * @method static bool anyFilled(string|array $keys)
 * @method static \Illuminate\Http\Request|mixed whenFilled(string $key, callable $callback, callable|null $default = null)
 * @method static bool missing(string|array $key)
 * @method static \Illuminate\Http\Request|mixed whenMissing(string $key, callable $callback, callable|null $default = null)
 * @method static \Illuminate\Support\Stringable str(string $key, mixed $default = null)
 * @method static \Illuminate\Support\Stringable string(string $key, mixed $default = null)
 * @method static bool boolean(string|null $key = null, bool $default = false)
 * @method static int integer(string $key, int $default = 0)
 * @method static float float(string $key, float $default = 0)
 * @method static float|int clamp(string $key, int|float $min, int|float $max, int|float $default = 0)
 * @method static \Illuminate\Support\Carbon|null date(string $key, string|null $format = null, \UnitEnum|string|null $tz = null)
 * @method static \Carbon\CarbonInterval|null interval(string $key, \Carbon\Unit|string|null $unit = null)
 * @method static \BackedEnum|\BackedEnum|null enum(string $key, string $enumClass, \BackedEnum|null $default = null)
 * @method static \BackedEnum[] enums(string $key, string $enumClass)
 * @method static array array(array|string|null $key = null)
 * @method static \Illuminate\Support\Collection collect(array|string|null $key = null)
 * @method static array only(mixed $keys)
 * @method static array except(mixed $keys)
 * @method static \Illuminate\Http\Request|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Illuminate\Http\Request|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static array validate(array $rules, ...$params)
 * @method static array validateWithBag(string $errorBag, array $rules, ...$params)
 * @method static bool hasValidRelativeSignature()
 * @method static bool hasValidSignatureWhileIgnoring($ignoreQuery = [], $absolute = true)
 * @method static bool hasValidRelativeSignatureWhileIgnoring($ignoreQuery = [])
 *
 * @see \Illuminate\Http\Request
 */
class LaravelRequest
{
    /**
     * 最近一次生效的调用类（含子类覆盖场景），供 Mock\Request::capture()/createFromGlobals() 回溯覆盖实现
     */
    protected static ?string $calledFacadeClass = null;

    public static function getCalledFacadeClass(): string
    {
        return self::$calledFacadeClass ?? self::class;
    }

    public static function instance(): BaseRequest
    {
        self::$calledFacadeClass = static::class;
        self::registerValidate();

        return static::createFromGlobal();
    }

    /**
     * 覆写为真实方法：覆盖场景下（继承本类覆盖 createFromGlobal）若经 __callStatic 落到
     * Illuminate\Http\Request 原生 capture()/createFromGlobals()，会绕过运行时探测直接读 PHP superglobals（webman 下错误）
     */
    public static function capture(): BaseRequest
    {
        self::$calledFacadeClass = static::class;

        return static::createFromGlobal();
    }

    public static function createFromGlobals(): BaseRequest
    {
        self::$calledFacadeClass = static::class;

        return static::createFromGlobal();
    }

    /**
     * 从当前运行时的原始请求创建（经 CommonUtils\Request 探测）
     *
     * 支持配置 request.create_from_global 自定义（闭包需返回 Illuminate\Http\Request）
     * 可通过继承本类覆盖此方法实现自定义创建逻辑
     */
    public static function createFromGlobal(): BaseRequest
    {
        $createFromGlobal = ConfigHelper::get('request.create_from_global');
        if ($createFromGlobal instanceof Closure) {
            /** @phpstan-ignore-next-line */
            return $createFromGlobal();
        }

        $rawRequest = UtilsRequest::getCurrent()?->getRaw();
        // 原始请求已是 Mock\Request 实例，直接返回
        if ($rawRequest instanceof IlluminateRequest) {
            return $rawRequest;
        }
        // Illuminate\Http\Request 继承自 Symfony Request，需先判断
        if ($rawRequest instanceof BaseRequest) {
            return IlluminateRequest::createFrom($rawRequest);
        }
        if ($rawRequest instanceof SymfonyRequest) {
            return IlluminateRequest::createFromBase($rawRequest);
        }
        if ($rawRequest instanceof WebmanRequest) {
            return static::createFromWebman($rawRequest);
        }
        // 无请求（CLI 等场景）或无法识别的请求假体，回退空 Request
        return IlluminateRequest::create('');
    }

    /**
     * 从 webman 请求创建
     *
     * 支持配置 request.create_from_webman 自定义（闭包需返回 Illuminate\Http\Request）
     * 可通过继承本类覆盖此方法实现自定义转换逻辑
     */
    public static function createFromWebman(WebmanRequest|null $webmanRequest = null): BaseRequest
    {
        $createFromWebman = ConfigHelper::get('request.create_from_webman');
        if ($createFromWebman instanceof Closure) {
            /** @phpstan-ignore-next-line */
            return $createFromWebman($webmanRequest);
        }

        if (!class_exists(WebmanRequest::class)) {
            return IlluminateRequest::create('');
        }
        $webmanRequest = $webmanRequest ?: \request();
        if (!$webmanRequest instanceof WebmanRequest) {
            return IlluminateRequest::create('');
        }

        $server = array_filter([
            // in symfony create
            'SERVER_NAME' => $webmanRequest->getLocalIp(),
            'SERVER_PORT' => $webmanRequest->getLocalPort(),
            'HTTP_HOST' => $webmanRequest->host(),
            'HTTP_USER_AGENT' => $webmanRequest->header('user-agent'),
            'HTTP_ACCEPT' => $webmanRequest->header('accept'),
            'HTTP_ACCEPT_LANGUAGE' => $webmanRequest->header('accept-language'),
            'HTTP_ACCEPT_CHARSET' => $webmanRequest->header('accept-charset'),
            'REMOTE_ADDR' => $webmanRequest->getRemoteIp(),
            'SCRIPT_NAME' => '',
            'SCRIPT_FILENAME' => '',
            'SERVER_PROTOCOL' => 'HTTP/' . $webmanRequest->protocolVersion(),
            'REQUEST_TIME' => '',
            'REQUEST_TIME_FLOAT' => '',
            // others
            'QUERY_STRING' => $webmanRequest->queryString(),
            'HTTPS' => $webmanRequest->header('https') ?: $webmanRequest->getLocalPort() == 443,
            'SERVER_ADDR' => $webmanRequest->getLocalIp(),
            'REQUEST_METHOD' => strtoupper($webmanRequest->method()),
            'REQUEST_URI' => $webmanRequest->uri(),
        ], fn($v) => $v !== null && $v !== '');
        foreach ($webmanRequest->header() as $key => $value) {
            $server['HTTP_' . str_replace('-', '_', strtoupper($key))] = $value;
        }

        $files = $webmanRequest->file();
        if ($files instanceof WebmanUploadFile) {
            $files = [$files];
        }
        if (is_array($files)) {
            foreach ($files as &$file) {
                $file = LaravelUploadedFile::createForSymfonyFromWebman($file);
            }
            unset($file);
        }

        return IlluminateRequest::create(
            $webmanRequest->uri(),
            $webmanRequest->method(),
            $webmanRequest->method() === 'GET' ? $webmanRequest->get() : $webmanRequest->post(),
            $webmanRequest->cookie(),
            $files,
            $server,
            $webmanRequest->rawBody()
        );
    }

    protected static function registerValidate(): void
    {
        if (IlluminateRequest::hasMacro('validate')) {
            return;
        }

        /**
         * https://github.com/laravel/framework/blob/11.x/src/Illuminate/Foundation/Providers/FoundationServiceProvider.php#L145
         */
        IlluminateRequest::macro('validate', function (array $rules, ...$params) {
            /** @var IlluminateRequest $this */
            $validator = ExtComponentGetter::get(ValidatorFactory::class);
            return $validator->make($this->all(), $rules, ...$params)->validate();
        });

        IlluminateRequest::macro('validateWithBag', function (string $errorBag, array $rules, ...$params) {
            /** @var IlluminateRequest $this */
            try {
                return $this->validate($rules, ...$params);
                /** @phpstan-ignore-next-line */
            } catch (ValidationException $e) {
                $e->errorBag = $errorBag;

                throw $e;
            }
        });
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return self::instance()->{$name}(...$arguments);
    }
}
