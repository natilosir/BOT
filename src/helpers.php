<?php

use Illuminate\Container\Container;
use natilosir\bot\Bootstrap;
use natilosir\bot\log\Log;

if ( !function_exists('lg') ) {
    function lg( ...$data ): void {
        foreach ( $data as $d ) {
            Log::write('DEBUG', $d);
        }
    }
}

if ( !function_exists('log') ) {
    function log( ...$data ): void {
        foreach ( $data as $d ) {
            Log::write('DEBUG', $d);
        }
    }
}

if ( !function_exists('dad') ) {
    function dad( ...$data ): never {
        foreach ( $data as $d ) {
            Log::write('DEBUG', $d);
        }
        exit;
    }
}

if ( !function_exists('dd') ) {
    function dd( ...$vars ): never {
        foreach ( $vars as $v ) {
            Log::debug($v);
        }
        die;
    }
}

if ( !function_exists('app') ) {
    function app( $abstract = null, array $parameters = [] ) {
        if ( is_null($abstract) ) {
            return Container::getInstance();
        }

        return Container::getInstance()
            ->make($abstract, $parameters);
    }
}

if ( !function_exists('paths') ) {
    function paths(): object {
        static $instance = null;

        if ( $instance === null ) {
            $instance = new class {
                private array $map = [
                    'base'    => 'base_path',
                    'app'     => 'app_path',
                    'route'   => 'route_path',
                    'router'  => 'route_path',
                    'config'  => 'config_path',
                    'storage' => 'storage_path',
                    'log'     => 'log_path',
                    'logs'    => 'log_path',
                ];

                public function __get( string $name ): string {
                    return $this->resolve($name);
                }

                public function __call( string $name, array $arguments ): string {
                    return $this->resolve($name, (string) ( $arguments[0] ?? '' ));
                }

                public function config( string $key = '', mixed $default = null ): mixed {
                    $app = Bootstrap::getInstance();

                    if ( !$app instanceof Bootstrap ) {
                        throw new RuntimeException('Bootstrap هنوز راه‌اندازی نشده است.');
                    }

                    if ( $key === 'bot.token' ) {
                        $driver = strtolower((string) $app->config('bot.default', 'telegram'));

                        return $app->config("bot.drivers.{$driver}.token", $default);
                    }

                    return $app->config($key, $default);
                }

                private function resolve( string $name, string $path = '' ): string {
                    $app = Bootstrap::getInstance();

                    if ( !$app instanceof Bootstrap ) {
                        throw new RuntimeException('Bootstrap هنوز راه‌اندازی نشده است.');
                    }

                    return $app->path($this->map[$name] ?? $name, $path);
                }
            };
        }

        return $instance;
    }
}