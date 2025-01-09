<?php

namespace WebmanTech\LaravelHttp\Facades;

use Closure;
use Illuminate\Contracts\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\ValidationException;
use WebmanTech\LaravelHttp\Helper\ConfigHelper;
use WebmanTech\LaravelHttp\Helper\ExtComponentGetter;
use WebmanTech\LaravelHttp\Request as IlluminateRequest;
use WebmanTech\LaravelValidation\Facades\Validator;

/**
 * @method static \Illuminate\Http\Request capture()
 * @method static string method()
 * @method static \Illuminate\Support\Uri uri()
 * @method static string root()
 * @method static string url()
 * @method static string fullUrl()
 * @method static string fullUrlWithQuery(array $query)
 * @method static string fullUrlWithoutQuery(array| $keys)
 * @method static string path()
 * @method static string decodedPath()
 * @method static string|null segment(int $index, $default = null)
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
 * @method static \Illuminate\Http\Request merge(array $input)
 * @method static \Illuminate\Http\Request mergeIfMissing(array $input)
 * @method static \Illuminate\Http\Request replace(array $input)
 * @method static mixed get(string $key, $default = null)
 * @method static \Symfony\Component\HttpFoundation\InputBag|mixed json(string|null $key = null, mixed $default = null)
 * @method static \Illuminate\Http\Request createFrom($from, |null $to = null)
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
 * @method static \Illuminate\Http\Request createFromGlobals()
 * @method static \Illuminate\Http\Request create(string $uri, string $method = 'GET', array $parameters = [], array $cookies = [], array $files = [], array $server = [], string|resource|null $content = null)
 * @method static void setFactory(callable|null $callable)
 * @method static void overrideGlobals()
 * @method static void setTrustedProxies(array $proxies, int $trustedHeaderSet)
 * @method static string[] getTrustedProxies()
 * @method static int getTrustedHeaderSet()
 * @method static void setTrustedHosts(array $hostPatterns)
 * @method static string[] getTrustedHosts()
 * @method static string normalizeQueryString(|null $qs)
 * @method static void enableHttpMethodParameterOverride()
 * @method static bool getHttpMethodParameterOverride()
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
 * @method static string getUriForPath($path)
 * @method static string getRelativeUriForPath($path)
 * @method static string|null getQueryString()
 * @method static bool isSecure()
 * @method static string getHost()
 * @method static void setMethod(string $method)
 * @method static string getMethod()
 * @method static string getRealMethod()
 * @method static string|null getMimeType(string $format)
 * @method static string[] getMimeTypes(string $format)
 * @method static string|null getFormat($mimeType)
 * @method static void setFormat(string|null $format, string|string[] $mimeTypes)
 * @method static string|null getRequestFormat($default = 'html')
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
 * @method static string|null getPreferredFormat($default = 'html')
 * @method static string|null getPreferredLanguage(string[] $locales = null)
 * @method static string[] getLanguages()
 * @method static string[] getCharsets()
 * @method static string[] getEncodings()
 * @method static string[] getAcceptableContentTypes()
 * @method static bool isXmlHttpRequest()
 * @method static bool preferSafeContent()
 * @method static bool isFromTrustedProxy()
 * @method static array filterPrecognitiveRules($rules)
 * @method static bool isAttemptingPrecognition()
 * @method static bool isPrecognitive()
 * @method static bool isJson()
 * @method static bool expectsJson()
 * @method static bool wantsJson()
 * @method static bool accepts(string|array $contentTypes)
 * @method static string|null prefers(string|array $contentTypes)
 * @method static bool acceptsAnyContentType()
 * @method static bool acceptsJson()
 * @method static bool acceptsHtml()
 * @method static bool matchesType(string $actual, string $type)
 * @method static string format($default = 'html')
 * @method static string|array|null old(string|null $key = null, \Illuminate\Database\Eloquent\Model| $default = null)
 * @method static void flash()
 * @method static void flashOnly(array|mixed $keys)
 * @method static void flashExcept(array|mixed $keys)
 * @method static void flush()
 * @method static string|array|null server(string|null $key = null, $default = null)
 * @method static bool hasHeader(string $key)
 * @method static string|array|null header(string|null $key = null, $default = null)
 * @method static string|null bearerToken()
 * @method static array keys()
 * @method static array all(|mixed|null $keys = null)
 * @method static mixed input(string|null $key = null, $default = null)
 * @method static \Illuminate\Support\Fluent fluent(array|string|null $key = null)
 * @method static string|array|null query(string|null $key = null, $default = null)
 * @method static string|array|null post(string|null $key = null, $default = null)
 * @method static bool hasCookie(string $key)
 * @method static string|array|null cookie(string|null $key = null, $default = null)
 * @method static array allFiles()
 * @method static bool hasFile(string $key)
 * @method static \Illuminate\Http\UploadedFile|\Illuminate\Http\UploadedFile[]|array|null file(string|null $key = null, mixed $default = null)
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
 * @method static bool ean(string|null $key = null, $default = false)
 * @method static int eger(string $key, $default = 0)
 * @method static float (string $key, $default = 0)
 * @method static \Illuminate\Support\Carbon|null date(string $key, string|null $format = null, string|null $tz = null)
 * @method static \BackedEnum|null enum(string $key, string $enumClass)
 * @method static \BackedEnum[] enums(string $key, string $enumClass)
 * @method static \Illuminate\Support\Collection collect(array|string|null $key = null)
 * @method static array only(|mixed $keys)
 * @method static array except(|mixed $keys)
 * @method static \Illuminate\Http\Request|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \Illuminate\Http\Request|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static array validate($rules, ...$params)
 * @method static array validateWithBag(string $errorBag, $rules, ...$params)
 *
 * @see \Illuminate\Http\Request
 * @see \WebmanTech\LaravelHttp\Request
 * @method static \Illuminate\Http\Request createFromWebman(\Webman\Http\Request|null $request = null)
 */
class LaravelRequest
{
    public static function instance(): IlluminateRequest
    {
        self::registerCreateFromWebman();
        self::registerValidate();

        return IlluminateRequest::createFromWebman();
    }

    protected static function registerCreateFromWebman(): void
    {
        if (IlluminateRequest::hasMacro('createFromWebman')) {
            return;
        }

        $createFromWebman = ConfigHelper::get('request.create_from_webman');
        if (!$createFromWebman instanceof Closure) {
            $createFromWebman = self::getDefaultCreateFromWebman();
        }

        IlluminateRequest::macro('createFromWebman', $createFromWebman);
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
            $validator = ExtComponentGetter::get(ValidatorFactory::class, [Validator::class, fn() => Validator::instance()]);
            return $validator->make($this->all(), $rules, ...$params)->validate();
        });

        IlluminateRequest::macro('validateWithBag', function (string $errorBag, array $rules, ...$params) {
            /** @var IlluminateRequest $this */
            try {
                return $this->validate($rules, ...$params);
            } catch (ValidationException $e) {
                $e->errorBag = $errorBag;

                throw $e;
            }
        });
    }

    protected static function getDefaultCreateFromWebman(): Closure
    {
        return function ($wRequest = null) {
            $wRequest = $wRequest ?: request();
            if (!$wRequest instanceof \Webman\Http\Request) {
                return IlluminateRequest::create('');
            }

            $server = array_filter([
                // in symfony create
                'SERVER_NAME' => $wRequest->getLocalIp(),
                'SERVER_PORT' => $wRequest->getLocalPort(),
                'HTTP_HOST' => $wRequest->host(),
                'HTTP_USER_AGENT' => $wRequest->header('user-agent'),
                'HTTP_ACCEPT' => $wRequest->header('accept'),
                'HTTP_ACCEPT_LANGUAGE' => $wRequest->header('accept-language'),
                'HTTP_ACCEPT_CHARSET' => $wRequest->header('accept-charset'),
                'REMOTE_ADDR' => $wRequest->getRemoteIp(),
                'SCRIPT_NAME' => '',
                'SCRIPT_FILENAME' => '',
                'SERVER_PROTOCOL' => 'HTTP/' . $wRequest->protocolVersion(),
                'REQUEST_TIME' => '',
                'REQUEST_TIME_FLOAT' => '',
                // others
                'QUERY_STRING' => $wRequest->queryString(),
                'HTTPS' => $wRequest->header('https') ?: $wRequest->getLocalPort() == 443,
                'SERVER_ADDR' => $wRequest->getLocalIp(),
                'REQUEST_METHOD' => strtoupper($wRequest->method()),
                'REQUEST_URI' => $wRequest->uri(),
            ], fn($v) => $v !== null && $v !== '');
            foreach ($wRequest->header() as $key => $value) {
                $server['HTTP_' . str_replace('-', '_', strtoupper($key))] = $value;
            }

            return IlluminateRequest::create(
                $wRequest->uri(),
                $wRequest->method(),
                $wRequest->method() === 'GET' ? $wRequest->get() : $wRequest->post(),
                $wRequest->cookie(),
                $wRequest->file(),
                $server,
                $wRequest->rawBody()
            );
        };
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return self::instance()->{$name}(...$arguments);
    }
}
