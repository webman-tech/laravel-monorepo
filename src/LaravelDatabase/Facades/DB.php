<?php

namespace WebmanTech\LaravelDatabase\Facades;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\DatabaseManager;
use WebmanTech\CommonUtils\Container;
use WebmanTech\LaravelDatabase\Mock\DefaultDatabaseManager;

/**
 * @method static \Illuminate\Database\Connection connection(\UnitEnum|string|null $name = null)
 * @method static \Illuminate\Database\ConnectionInterface build(array $config)
 * @method static string calculateDynamicConnectionName(array $config)
 * @method static \Illuminate\Database\ConnectionInterface connectUsing(\UnitEnum|string $name, array $config, bool $force = false)
 * @method static void purge(\UnitEnum|string|null $name = null)
 * @method static void disconnect(\UnitEnum|string|null $name = null)
 * @method static \Illuminate\Database\Connection reconnect(\UnitEnum|string|null $name = null)
 * @method static mixed usingConnection(\UnitEnum|string $name, callable $callback)
 * @method static string getDefaultConnection()
 * @method static void setDefaultConnection(string $name)
 * @method static string[] supportedDrivers()
 * @method static string[] availableDrivers()
 * @method static void extend(string $name, callable $resolver)
 * @method static void forgetExtension(string $name)
 * @method static array getConnections()
 * @method static void setReconnector(callable $reconnector)
 * @method static \Illuminate\Database\DatabaseManager setApplication(\Illuminate\Contracts\Foundation\Application $app)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 * @method static void useDefaultQueryGrammar()
 * @method static void useDefaultSchemaGrammar()
 * @method static void useDefaultPostProcessor()
 * @method static \Illuminate\Database\Schema\Builder getSchemaBuilder()
 * @method static \Illuminate\Database\Query\Builder table(\Closure|\Illuminate\Database\Query\Builder|\Illuminate\Contracts\Database\Query\Expression|\UnitEnum|string $table, string|null $as = null)
 * @method static \Illuminate\Database\Query\Builder query()
 * @method static mixed selectOne(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static mixed scalar(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static array selectFromWriteConnection(string $query, array $bindings = [])
 * @method static array select(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static array selectResultSets(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static \Generator cursor(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static bool insert(string $query, array $bindings = [])
 * @method static int update(string $query, array $bindings = [])
 * @method static int delete(string $query, array $bindings = [])
 * @method static bool statement(string $query, array $bindings = [])
 * @method static int affectingStatement(string $query, array $bindings = [])
 * @method static bool unprepared(string $query)
 * @method static int|null threadCount()
 * @method static array[] pretend(\Closure $callback)
 * @method static mixed withoutPretending(\Closure $callback)
 * @method static void bindValues(\PDOStatement $statement, array $bindings)
 * @method static array prepareBindings(array $bindings)
 * @method static void logQuery(string $query, array $bindings, float|null $time = null)
 * @method static void whenQueryingForLongerThan(\DateTimeInterface|\Carbon\CarbonInterval|float|int $threshold, callable $handler)
 * @method static void allowQueryDurationHandlersToRunAgain()
 * @method static float totalQueryDuration()
 * @method static void resetTotalQueryDuration()
 * @method static void reconnectIfMissingConnection()
 * @method static \Illuminate\Database\Connection beforeStartingTransaction(\Closure $callback)
 * @method static \Illuminate\Database\Connection beforeExecuting(\Closure $callback)
 * @method static void listen(\Closure $callback)
 * @method static \Illuminate\Contracts\Database\Query\Expression raw(mixed $value)
 * @method static string escape(string|float|int|bool|null $value, bool $binary = false)
 * @method static bool hasModifiedRecords()
 * @method static void recordsHaveBeenModified(bool $value = true)
 * @method static \Illuminate\Database\Connection setRecordModificationState(bool $value)
 * @method static void forgetRecordModificationState()
 * @method static \Illuminate\Database\Connection useWriteConnectionWhenReading(bool $value = true)
 * @method static \PDO getPdo()
 * @method static \PDO|\Closure|null getRawPdo()
 * @method static \PDO getReadPdo()
 * @method static \PDO|\Closure|null getRawReadPdo()
 * @method static \Illuminate\Database\Connection setPdo(\PDO|\Closure|null $pdo)
 * @method static \Illuminate\Database\Connection setReadPdo(\PDO|\Closure|null $pdo)
 * @method static \Illuminate\Database\Connection setReadPdoConfig(array $config)
 * @method static string|null getName()
 * @method static string|null getNameWithReadWriteType()
 * @method static mixed getConfig(string|null $option = null)
 * @method static string getDriverName()
 * @method static string getDriverTitle()
 * @method static \Illuminate\Database\Query\Grammars\Grammar getQueryGrammar()
 * @method static \Illuminate\Database\Connection setQueryGrammar(\Illuminate\Database\Query\Grammars\Grammar $grammar)
 * @method static \Illuminate\Database\Schema\Grammars\Grammar getSchemaGrammar()
 * @method static \Illuminate\Database\Connection setSchemaGrammar(\Illuminate\Database\Schema\Grammars\Grammar $grammar)
 * @method static \Illuminate\Database\Query\Processors\Processor getPostProcessor()
 * @method static \Illuminate\Database\Connection setPostProcessor(\Illuminate\Database\Query\Processors\Processor $processor)
 * @method static \Illuminate\Contracts\Events\Dispatcher|null getEventDispatcher()
 * @method static \Illuminate\Database\Connection setEventDispatcher(\Illuminate\Contracts\Events\Dispatcher $events)
 * @method static void unsetEventDispatcher()
 * @method static \Illuminate\Database\Connection setTransactionManager(\Illuminate\Database\DatabaseTransactionsManager $manager)
 * @method static void unsetTransactionManager()
 * @method static bool pretending()
 * @method static array[] getQueryLog()
 * @method static array getRawQueryLog()
 * @method static void flushQueryLog()
 * @method static void enableQueryLog()
 * @method static void disableQueryLog()
 * @method static bool logging()
 * @method static string getDatabaseName()
 * @method static \Illuminate\Database\Connection setDatabaseName(string $database)
 * @method static \Illuminate\Database\Connection setReadWriteType(string|null $readWriteType)
 * @method static string getTablePrefix()
 * @method static \Illuminate\Database\Connection setTablePrefix(string $prefix)
 * @method static mixed withoutTablePrefix(\Closure $callback)
 * @method static string getServerVersion()
 * @method static void resolverFor(string $driver, \Closure $callback)
 * @method static \Closure|null getResolver(string $driver)
 * @method static mixed transaction(\Closure $callback, int $attempts = 1)
 * @method static void beginTransaction()
 * @method static void commit()
 * @method static void rollBack(int|null $toLevel = null)
 * @method static int transactionLevel()
 * @method static void afterCommit(callable $callback)
 * @method static void afterRollBack(callable $callback)
 */
class DB
{
    private static $instance;

    /**
     * @return DatabaseManager
     */
    public static function instance(): ConnectionResolverInterface
    {
        if (self::$instance !== null) {
            return self::$instance;
        }
        $container = Container::getCurrent();
        if ($container->has(ConnectionResolverInterface::class)) {
            /** @phpstan-ignore-next-line */
            return self::$instance = $container->get(ConnectionResolverInterface::class);
        }
        return self::$instance = self::resolveDefault();
    }

    /**
     * 组件内默认解析（不含容器与静态缓存）：webman 全局实例 → 纯 illuminate 默认（config('database') 驱动）
     *
     * @return DatabaseManager
     */
    public static function resolveDefault(): ConnectionResolverInterface
    {
        $webmanManager = self::resolveWebmanManager();
        if ($webmanManager !== null) {
            return $webmanManager;
        }
        return DefaultDatabaseManager::create();
    }

    /**
     * 兼容历史版本 API（曾读取 webman 全局 Manager 实例），现等价于 instance()
     *
     * @return DatabaseManager
     */
    public static function getInstance(): ConnectionResolverInterface
    {
        return static::instance();
    }

    /**
     * webman 环境下从 support\Db 的全局实例取 DatabaseManager，未初始化时返回 null
     */
    private static function resolveWebmanManager(): ?DatabaseManager
    {
        if (!class_exists('support\Db')) {
            return null;
        }
        try {
            /** @var \support\Db|null $instance */
            $instance = (new \ReflectionProperty('support\Db', 'instance'))->getValue();
            return $instance?->getDatabaseManager();
        } catch (\ReflectionException) {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public static function __callStatic($name, array $arguments)
    {
        return static::instance()->{$name}(...$arguments);
    }
}
