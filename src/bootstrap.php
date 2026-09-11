<?php

namespace natilosir\bot;

use DateTimeZone;
use Illuminate\Container\Container;
use natilosir\bot\log\AdvancedLogger;
use natilosir\Verta\Verta;
use RuntimeException;

class Bootstrap extends Container {

    public function __construct( array $paths = [] ) {
        Container::setInstance($this);
        $this->resolvePaths($paths);
        $this->registerBaseBindings();
        $this->loadConfig();
        $this->loadFiles();
        $this->loadClasses();
    }

    protected array $defaultPaths = [
        'base_path'    => '',
        'app_path'     => 'app',
        'route_path'   => 'Router',
        'config_path'  => 'config',
        'storage_path' => 'storage',
        'log_path'     => 'storage/logs',
    ];

    protected array $paths   = [];
    protected array $config  = [
        'timezone' => 'Asia/Tehran',
        'locale'   => 'fa',
        'calendar' => 'jalali',
    ];
    protected array $files   = [
        'route.php',
    ];
    protected array $classes = [
        AdvancedLogger::class,
    ];

    protected function resolvePaths( array $custom ): void {
        $basePath = $custom['base_path'] ?? dirname(__DIR__);
        $basePath = rtrim($basePath, '/\\');

        $merged = $this->defaultPaths;
        foreach ( $custom as $key => $value ) {
            if ( $value !== null && $value !== '' ) {
                $merged[$key] = $value;
            }
        }
        $merged['base_path'] = $basePath;

        $this->paths = [];
        foreach ( $merged as $name => $value ) {
            if ( $name === 'base_path' ) {
                $this->paths['base_path'] = $basePath;
                continue;
            }

            $this->paths[$name] = $this->isAbsolutePath($value) ? rtrim($value, '/\\') : $basePath . DIRECTORY_SEPARATOR . ltrim($value, '/\\');
        }

        foreach ( $this->paths as $name => $value ) {
            $this->instance("path.{$name}", $value);
        }
    }

    public function setPath( string $name, string $path ): static {
        $full = $this->isAbsolutePath($path) ? rtrim($path, '/\\') : $this->paths['base_path'] . DIRECTORY_SEPARATOR . ltrim($path, '/\\');

        $this->paths[$name] = $full;
        $this->instance("path.{$name}", $full);

        return $this;
    }

    public function setBasePath( string $path ): static {
        $path                     = rtrim($path, '/\\');
        $this->paths['base_path'] = $path;
        $this->instance('path.base_path', $path);

        return $this;
    }

    public function basePath( string $path = '' ): string {
        return $this->joinPath($this->paths['base_path'] ?? '', $path);
    }

    public function appPath( string $path = '' ): string {
        return $this->joinPath($this->paths['app_path'] ?? '', $path);
    }

    public function routePath( string $path = '' ): string {
        return $this->joinPath($this->paths['route_path'] ?? '', $path);
    }

    public function configPath( string $path = '' ): string {
        return $this->joinPath($this->paths['config_path'] ?? '', $path);
    }

    public function storagePath( string $path = '' ): string {
        return $this->joinPath($this->paths['storage_path'] ?? '', $path);
    }

    public function logPath( string $path = '' ): string {
        return $this->joinPath($this->paths['log_path'] ?? '', $path);
    }

    public function path( string $name, string $path = '' ): string {
        return $this->joinPath($this->paths[$name] ?? '', $path);
    }

    public function paths(): array {
        return $this->paths;
    }

    protected function registerBaseBindings(): void {
        $this->instance('app', $this);
        $this->instance('bootstrap', $this);
        $this->instance(static::class, $this);
        $this->instance(Container::class, $this);
    }

    protected function loadConfig(): void {
        if ( !empty($this->config['timezone']) ) {
            date_default_timezone_set($this->config['timezone']);
        }

        if ( class_exists(Verta::class, false) ) {
            try {
                Verta::setTimezone(new DateTimeZone($this->config['timezone']));
            } catch ( \Throwable $e ) {
            }
            try {
                if ( !empty($this->config['locale']) ) Verta::setLocale($this->config['locale']);
            } catch ( \Throwable $e ) {
            }
        }

        $GLOBALS['__APP_CONFIG__'] = $this->config;
    }

    public function config( string $key, mixed $default = null ): mixed {
        return $this->config[$key] ?? $default;
    }

    protected function loadFiles(): void {
        foreach ( $this->files as $file ) {
            $fullPath = $this->routePath($file);

            if ( !file_exists($fullPath) ) {
                throw new RuntimeException("فایل مورد نیاز پیدا نشد: {$fullPath}");
            }

            require_once $fullPath;
        }
    }

    protected function loadClasses(): void {
        foreach ( $this->classes as $class ) {
            if ( !class_exists($class) ) {
                throw new RuntimeException("کلاس مورد نیاز پیدا نشد: {$class}");
            }

            if ( method_exists($class, 'getInstance') ) {
                $this->singleton($class, fn() => $class::getInstance());
                $class::getInstance();
                continue;
            }

            $this->singleton($class, fn() => new $class());
            $this->make($class);
        }
    }

    public static function getInstance(): ?self {
        return static::$instance instanceof self ? static::$instance : null;
    }

    protected function isAbsolutePath( string $path ): bool {
        return (bool) preg_match('#^(?:[A-Za-z]:[\\\\/]|/|\\\\)#', $path);
    }

    protected function joinPath( string $base, string $path ): string {
        if ( $path === '' ) return $base;
        return rtrim($base, '/\\') . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
    }
}