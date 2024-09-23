<?php


declare(strict_types=1);

namespace Debuggertools\Config;

class InstanceConfig
{
    // Propriété statique accessible globalement
    public static $settings = [];

    // Méthode statique pour obtenir un paramètre
    public static function get(string $key)
    {
        return self::$settings[$key] ?? null;
    }

    // Méthode statique pour définir un paramètre
    public static function set(string $key, $value): void
    {
        self::$settings[$key] = $value;
    }

    // Méthode statique pour définir un paramètre
    public static function reset(): void
    {
        self::$settings = [];
    }
}
