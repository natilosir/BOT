<?php

namespace natilosir\bot;

use Illuminate\Http\Client\Factory;
use natilosir\bot\http\PendingRequest;

/**
 * Static HTTP gateway backed by Illuminate HTTP Client, without Facades/Container.
 *
 * @mixin PendingRequest
 */
final class Http {
    private static ?Factory $factory = null;

    public static function __callStatic( string $method, array $parameters ): mixed {
        $request = new PendingRequest(self::$factory ??= new Factory());
        return $request->{$method}(...$parameters);
    }
}
