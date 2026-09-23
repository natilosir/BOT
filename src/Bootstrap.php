<?php

namespace natilosir\bot;

use Illuminate\Container\Container;
use Illuminate\Http\Client\Factory;
use InvalidArgumentException;
use natilosir\bot\bot\BotManager;
use natilosir\bot\bot\DriverManager;
use RuntimeException;

/**
 * Application bootstrap for standalone bot projects.
 *
 * Responsibilities:
 * - Own application paths and configuration.
 * - Initialize the Illuminate container.
 * - Register core bot services.
 * - Connect package facades to the container.
 * - Apply process-level configuration such as timezone.
 * - Auto-load route (and other) files from the route path.
 *
 * Platform-specific behavior intentionally lives in the bot drivers, not here.
 */
final class Bootstrap {
    /**
     * @param array<string, string> $paths
     */
    public function __construct( array $paths ) {
        $this->paths     = $this->normalizePaths($paths);
        $this->config    = $this->loadConfig();
        $this->container = new Container();

        Container::setInstance($this->container);
        self::$instance = $this;

        $this->registerCoreBindings();
        Facade::setFacadeApplication($this->container);
        $this->applyRuntimeConfiguration();
        $this->loadFiles();
    }

    private static ?self $instance = null;
    private Container    $container;
    private array        $paths;
    private array        $config   = [];
    private array        $files    = [
        'route.php',
    ];

    public static function getInstance(): ?self {
        return self::$instance;
    }

    public function container(): Container {
        return $this->container;
    }

    /**
     * Resolve an application path by its configured key.
     */
    public function path( string $key, string $append = '' ): string {
        if ( !array_key_exists($key, $this->paths) ) {
            throw new InvalidArgumentException("Application path [{$key}] is not configured.");
        }

        $base = $this->paths[$key];

        if ( $append === '' ) {
            return $base;
        }

        // config_path/log_path may point to files. Appending to a file path is
        // almost certainly a caller error, so keep path semantics explicit.
        if ( in_array($key, [ 'config_path', 'log_path' ], true) ) {
            throw new InvalidArgumentException("Cannot append a child path to [{$key}].");
        }

        return rtrim($base, '/\\') . DIRECTORY_SEPARATOR . ltrim($append, '/\\');
    }

    /**
     * Read configuration using dot notation.
     */
    public function config( string $key = '', mixed $default = null ): mixed {
        if ( $key === '' ) {
            return $this->config;
        }

        $value = $this->config;

        foreach ( explode('.', $key) as $segment ) {
            if ( !is_array($value) || !array_key_exists($segment, $value) ) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * @return array<string, string>
     */
    public function paths(): array {
        return $this->paths;
    }

    /**
     * Register additional files to be auto-loaded from the route path.
     *
     * @param array<int, string>|string $files
     */
    public function addFiles( array|string $files ): static {
        foreach ( (array) $files as $file ) {
            if ( !in_array($file, $this->files, true) ) {
                $this->files[] = $file;
            }
        }

        return $this;
    }

    /**
     * @param array<string, string> $paths
     * @return array<string, string>
     */
    private function normalizePaths( array $paths ): array {
        $basePath = $paths['base_path'] ?? null;

        if ( !is_string($basePath) || trim($basePath) === '' ) {
            throw new InvalidArgumentException('Bootstrap requires a non-empty [base_path].');
        }

        $basePath = rtrim($basePath, '/\\');

        $normalized = [
            'base_path'    => $basePath,
            'app_path'     => $paths['app_path'] ?? $basePath . DIRECTORY_SEPARATOR . 'app',
            'route_path'   => $paths['route_path'] ?? $basePath . DIRECTORY_SEPARATOR . 'Router',
            'config_path'  => $paths['config_path'] ?? $basePath . DIRECTORY_SEPARATOR . 'config.php',
            'storage_path' => $paths['storage_path'] ?? $basePath . DIRECTORY_SEPARATOR . 'storage',
            'log_path'     => $paths['log_path'] ?? $basePath . DIRECTORY_SEPARATOR . 'log.html',
        ];

        foreach ( $normalized as $key => $path ) {
            if ( !is_string($path) || trim($path) === '' ) {
                throw new InvalidArgumentException("Bootstrap path [{$key}] must be a non-empty string.");
            }

            $normalized[$key] = $key === 'base_path' ? rtrim($path, '/\\') : $this->normalizeConfiguredPath($path);
        }

        foreach ( $paths as $key => $path ) {
            if ( !array_key_exists($key, $normalized) && is_string($path) && trim($path) !== '' ) {
                $normalized[$key] = $this->normalizeConfiguredPath($path);
            }
        }

        return $normalized;
    }

    private function normalizeConfiguredPath( string $path ): string {
        if ( $path === DIRECTORY_SEPARATOR ) {
            return $path;
        }

        return rtrim($path, '/\\');
    }

    private function loadConfig(): array {
        $configPath = $this->paths['config_path'];

        if ( !is_file($configPath) ) {
            return [];
        }

        $config = require $configPath;

        if ( !is_array($config) ) {
            throw new RuntimeException("Configuration file [{$configPath}] must return an array.");
        }

        return $config;
    }

    private function registerCoreBindings(): void {
        $this->container->instance(self::class, $this);
        $this->container->instance(Container::class, $this->container);

        $this->container->singleton(Factory::class, static fn(): Factory => new Factory());

        $this->container->singleton(DriverManager::class, fn( Container $container ): DriverManager => new DriverManager($container->make(Factory::class), (array) $this->config('bot', [])));

        $this->container->singleton(BotManager::class, static fn( Container $container ): BotManager => new BotManager($container->make(DriverManager::class)));
    }

    private function applyRuntimeConfiguration(): void {
        $timezone = $this->config('timezone');

        if ( is_string($timezone) && $timezone !== '' ) {
            date_default_timezone_set($timezone);
        }
    }

    /**
     * Auto-load route files (and any other registered files) from the route path.
     *
     * Each entry may be:
     *  - a file name relative to route_path (e.g. "route.php")
     *  - an absolute path (starts with "/" or a Windows drive letter)
     */
    private function loadFiles(): void {
        $routePath = $this->paths['route_path'] ?? null;

        foreach ( $this->files as $file ) {
            if ( !is_string($file) || trim($file) === '' ) {
                continue;
            }

            $fullPath = $this->isAbsolutePath($file) ? $file : rtrim((string) $routePath, '/\\') . DIRECTORY_SEPARATOR . ltrim($file, '/\\');

            if ( !is_file($fullPath) ) {
                throw new RuntimeException("فایل مورد نیاز پیدا نشد: {$fullPath}");
            }

            require_once $fullPath;
        }
    }

    private function isAbsolutePath( string $path ): bool {
        return (bool) preg_match('#^(?:[A-Za-z]:[\\\\/]|/|\\\\)#', $path);
    }
}