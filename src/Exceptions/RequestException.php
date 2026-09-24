<?php

namespace natilosir\bot\Exceptions;

use natilosir\bot\http\Response;
use ReflectionClass;
use Throwable;

class RequestException extends \Exception {
    public function __construct( Response|string $response, int $code = 0, ?Throwable $previous = null ) {
        if ( $response instanceof Response ) {
            $message        = "HTTP request returned status code {$response->status()}.";
            $this->response = $response;
        }
        else {
            $message        = self::redact($response);
            $this->response = null;
        }

        if ( $previous !== null ) {
            self::redactThrowable($previous);
        }

        parent::__construct($message, $code, $previous);
    }

    public ?Response     $response;
    private static array $tokens = [];

    public static function registerToken( string $token ): void {
        if ( $token !== '' ) {
            self::$tokens[$token] = true;
        }
    }

    public static function forgetTokens(): void {
        self::$tokens = [];
    }

    public static function redact( string $message ): string {
        if ( self::$tokens === [] || $message === '' ) {
            return $message;
        }

        return str_replace(array_keys(self::$tokens), '<BOT_TOKEN>', $message);
    }

    private static function redactThrowable( Throwable $e ): void {
        $original = $e->getMessage();

        if ( $original === '' ) {
            return;
        }

        $redacted = self::redact($original);

        if ( $redacted === $original ) {
            return;
        }

        try {
            $ref = new ReflectionClass($e);

            if ( !$ref->hasProperty('message') ) {
                return;
            }

            $prop = $ref->getProperty('message');
            $prop->setAccessible(true);
            $prop->setValue($e, $redacted);
        } catch ( Throwable $e ) {
            dd($e);
        }
    }
}