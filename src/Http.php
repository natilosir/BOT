<?php

namespace Natilosir\Bot;

use Closure;

/**
 * @see PendingRequest
 *
 * @method static PendingRequest accept( string $contentType )
 * @method static PendingRequest acceptJson()
 * @method static PendingRequest asJson()
 * @method static PendingRequest asForm()
 * @method static PendingRequest baseUrl( string $url )
 * @method static PendingRequest withBody( string $content, string $contentType )
 * @method static PendingRequest withHeaders( array $headers )
 * @method static PendingRequest withOptions( array $options )
 * @method static PendingRequest withToken( string $token, string $type = 'Bearer' )
 * @method static PendingRequest withoutRedirecting()
 * @method static PendingRequest withoutVerifying()
 * @method static PendingRequest timeout( int $seconds )
 * @method static Response get( string $url, array|string|null $query = null )
 * @method static Response post( string $url, array $data = [] )
 * @method static Response patch( string $url, array $data = [] )
 * @method static Response put( string $url, array $data = [] )
 * @method static Response delete( string $url, array $data = [] )
 * @method static Response send( string $method, string $url, array $options = [] )
 */
class Http {
    /**
     * The factory callback array.
     *
     * @var Closure[]
     */
    protected static $factoryCallbacks = [];

    /**
     * Handle dynamic static method calls into the class.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public static function __callStatic( $method, $parameters ) {
        return static::newPendingRequest()->{$method}(...$parameters);
    }

    /**
     * Create a new pending request instance for this factory.
     *
     * @return PendingRequest
     */
    protected static function newPendingRequest() {
        $request = new PendingRequest();

        foreach ( static::$factoryCallbacks as $callback ) {
            $callback($request);
        }

        return $request;
    }
}
