<?php

namespace natilosir\bot;

use Illuminate\Container\Container;
use RuntimeException;

/**
 * Lightweight facade for standalone projects.
 * The application container must be initialized by Bootstrap constructor.
 */
abstract class Facade {
    protected static ?Container $app = null;

    public static function setFacadeApplication( Container $app ): void {
        static::$app = $app;
    }

    protected static function getFacadeAccessor() {
        throw new RuntimeException('Facade accessor not defined');
    }

    /**
     * Resolve the concrete service behind the facade.
     *
     * Kept protected so strongly typed facade accessors can reuse the same
     * container resolution path without duplicating container logic.
     */
    protected static function resolveFacadeRoot(): mixed {
        if ( !static::$app ) {
            throw new RuntimeException('Facade root has not been set. Create Bootstrap with your paths before using bot facade.');
        }

        return static::$app->make(static::getFacadeAccessor());
    }

    public static function __callStatic( $method, $args ) {
        $instance = static::resolveFacadeRoot();

        return $instance->{$method}(...$args);
    }
}
