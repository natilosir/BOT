<?php

namespace natilosir\bot\Model;

use Illuminate\Database\Capsule\Manager as Capsule;
use RuntimeException;

final class Database {
    private static bool $booted = false;

    public static function boot(): void {
        if ( self::$booted ) {
            return;
        }

        if ( !class_exists(Capsule::class) ) {
            throw new RuntimeException('illuminate/database is not installed. Run: composer install (or composer update illuminate/database).');
        }

        $db = paths()->config('database');

        if ( !is_array($db) ) {
            throw new RuntimeException("Database config not found in: " . paths()->config);
        }

        $capsule     = app(Capsule::class);
        $connections = self::normalizeConnections($db);
        $default     = $db['default'] ?? collect($connections)
            ->keys()
            ->first();

        collect($connections)
            ->filter(fn( $connection ) => is_array($connection))
            ->each(function ( array $connection, string $name ) use ( $capsule ): void {
                $capsule->addConnection(self::buildConnectionConfig($connection), $name);
            });

        $capsule->getDatabaseManager()
            ->setDefaultConnection((string) $default);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        self::$booted = true;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function normalizeConnections( array $db ): array {
        if ( isset($db['connections']) && is_array($db['connections']) ) {
            return $db['connections'];
        }

        $flat = collect($db)
            ->except([ 'default', 'connections' ])
            ->all();

        return [ 'default' => $flat ];
    }

    /**
     * @param array<string, mixed> $db
     * @return array<string, mixed>
     */
    private static function buildConnectionConfig( array $db ): array {
        collect([ 'host', 'database', 'user' ])->each(function ( string $key ) use ( $db ): void {
            if ( !array_key_exists($key, $db) ) {
                throw new RuntimeException("Missing database configuration key: {$key}");
            }
        });

        return [
            'driver'    => $db['driver'] ?? 'mysql',
            'host'      => $db['host'],
            'port'      => $db['port'] ?? 3306,
            'database'  => $db['database'],
            'username'  => $db['user'],
            'password'  => $db['password'] ?? '',
            'charset'   => $db['charset'] ?? 'utf8mb4',
            'collation' => $db['collation'] ?? 'utf8mb4_unicode_ci',
            'prefix'    => $db['prefix'] ?? '',
            'strict'    => $db['strict'] ?? true,
        ];
    }
}