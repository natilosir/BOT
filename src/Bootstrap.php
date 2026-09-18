<?php

namespace natilosir\bot;

use DateTimeZone;
use Illuminate\Container\Container;
use Illuminate\Http\Client\Factory;
use natilosir\bot\bot\BotManager;
use natilosir\bot\bot\TelegramClient;
use natilosir\bot\log\AdvancedLogger;
use natilosir\Verta\Verta;
use RuntimeException;
use Throwable;

class Bootstrap extends Container {

    public function __construct( array $paths = [] ) {
        Container::setInstance($this);
        Facade::setFacadeApplication($this);
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
        'config_path'  => 'config.php',
        'storage_path' => 'storage',
        'log_path'     => 'log.html',
    ];

    protected array $paths   = [];
    protected array $config  = [];
    protected array $files   = [
        'route.php',
    ];
    protected array $classes = [
        AdvancedLogger::class,
    ];

    protected ?array $dotConfig = null;

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

    protected function dotArray( array $array, string $prepend = '' ): array {
        $result = [];
        foreach ( $array as $key => $value ) {
            $newKey = $prepend === '' ? $key : $prepend . '.' . $key;
            if ( is_array($value) ) {
                $result += $this->dotArray($value, $newKey);
            }
            else {
                $result[$newKey] = $value;
            }
        }
        return $result;
    }

    protected function registerBaseBindings(): void {
        $this->instance('app', $this);
        $this->instance('bootstrap', $this);
        $this->instance(static::class, $this);
        $this->instance(Container::class, $this);
    }

    protected function loadConfig(): void {
        $configPath = $this->paths['config_path'] ?? null;

        try {
            if ( ( !is_file($configPath) && !is_dir($configPath) ) ) {
                throw new RuntimeException("Config file path not found: {$configPath}");
            }

            if ( is_file($configPath) ) {
                $values = require $configPath;

                if ( is_array($values) ) {
                    $this->config = $values;
                }
            }
            elseif ( is_dir($configPath) ) {
                $files = glob($configPath . DIRECTORY_SEPARATOR . '*.php') ? : [];

                $this->config = [];

                foreach ( $files as $file ) {
                    $key    = basename($file, '.php');
                    $values = require $file;

                    if ( !is_array($values) ) {
                        continue;
                    }

                    $this->config[$key] = $values;
                }
            }

            $this->dotConfig = null;

            if ( !empty($this->config['timezone']) ) {
                date_default_timezone_set($this->config['timezone']);
            }

            if ( class_exists(Verta::class, false) ) {
                try {
                    if ( !empty($this->config['timezone']) ) {
                        Verta::setTimezone(new DateTimeZone($this->config['timezone']));
                    }
                } catch ( Throwable $e ) {
                    dd($e);
                }
                try {
                    if ( !empty($this->config['locale']) ) {
                        Verta::setLocale($this->config['locale']);
                    }
                } catch ( Throwable $e ) {
                    dd($e);
                }
            }
        } catch ( Throwable $e ) {
            dd($e);
        }
    }

    public function config( string $key = '', mixed $default = null ): mixed {
        if ( $key === '' ) {
            return $this->config;
        }

        $this->dotConfig ??= $this->dotArray($this->config);

        if ( array_key_exists($key, $this->dotConfig) ) {
            return $this->dotConfig[$key];
        }

        $segments = explode('.', $key);
        $value    = $this->config;

        foreach ( $segments as $segment ) {
            if ( is_array($value) && array_key_exists($segment, $value) ) {
                $value = $value[$segment];
            }
            else {
                return $default;
            }
        }

        return $value;
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
        $this->registerBotBindings();

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

    protected function registerBotBindings(): void {
        if ( class_exists(TelegramClient::class) ) {
            $this->singleton(TelegramClient::class, function () {
                return new TelegramClient(app(Factory::class));
            });
        }

        if ( class_exists(BotManager::class) ) {
            $this->singleton(BotManager::class, function () {
                return new BotManager($this->make(TelegramClient::class));
            });
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