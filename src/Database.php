<?php

namespace natilosir\bot;

use Illuminate\Database\Capsule\Manager as Capsule;
use RuntimeException;

final class Database
{
    private static bool $booted = false;

    public static function boot(): void
    {
        if (self::$booted) {
            return;
        }

        if (!class_exists(Capsule::class)) {
            throw new RuntimeException(
                'illuminate/database is not installed. Run: composer install (or composer update illuminate/database).'
            );
        }

        $configFile = dirname(__DIR__, 4) . '/config.php';
        if (!is_file($configFile)) {
            throw new RuntimeException("Database config file not found: {$configFile}");
        }

        $config = require $configFile;
        $db = $config['database'] ?? [];

        foreach (['host', 'database', 'user'] as $key) {
            if (!array_key_exists($key, $db)) {
                throw new RuntimeException("Missing database configuration key: {$key}");
            }
        }

        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => $db['driver'] ?? 'mysql',
            'host' => $db['host'],
            'port' => $db['port'] ?? 3306,
            'database' => $db['database'],
            'username' => $db['user'],
            'password' => $db['password'] ?? '',
            'charset' => $db['charset'] ?? 'utf8mb4',
            'collation' => $db['collation'] ?? 'utf8mb4_unicode_ci',
            'prefix' => $db['prefix'] ?? '',
            'strict' => $db['strict'] ?? true,
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        self::$booted = true;
    }
}
