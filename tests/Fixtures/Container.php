<?php

namespace Tests\Fixtures;

final class Container extends \Illuminate\Container\Container
{
    private static array $rememberedProperties = [];

    public function __construct()
    {
        self::instance(\Illuminate\Contracts\Container\Container::class, $this);
    }

    public static function reset(): void
    {
        if (self::$rememberedProperties) {
            $instance = self::getInstance();
            foreach (self::$rememberedProperties as $name => $value) {
                $instance->{$name} = $value;
            }
        }
        self::$rememberedProperties = [];
    }

    public static function getTempInstance(): self
    {
        $reflection = new \ReflectionClass(self::class);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED);
        $instance = self::getInstance();
        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }
            self::$rememberedProperties[$property->getName()] = $instance->{$property->getName()};
        }

        return $instance;
    }
}
