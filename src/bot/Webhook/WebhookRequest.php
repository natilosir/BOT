<?php

namespace natilosir\bot\Bot\Webhook;

use Illuminate\Http\Request;

final class WebhookRequest {
    public function __construct( private readonly string $method, private readonly string $path, private readonly array $headers = [] ) {}

    public static function capture(): self {
        $request = Request::capture();

        $headers = [];
        foreach ( $request->headers->all() as $name => $values ) {
            $headers[strtolower($name)] = (string) ( $values[0] ?? '' );
        }

        return new self(strtoupper($request->getMethod()), self::normalizePath($request->getPathInfo() ? : '/'), $headers);
    }

    public function method(): string {
        return $this->method;
    }

    public function path(): string {
        return $this->path;
    }

    public function header( string $name ): ?string {
        return $this->headers[strtolower($name)] ?? null;
    }

    public function host(): string {
        $host = trim((string) ( $this->header('host') ?? '' ));
        if ( $host === '' ) {
            return '';
        }

        $parsed = parse_url('http://' . $host, PHP_URL_HOST);
        return strtolower((string) ( $parsed ? : $host ));
    }

    public function isPost(): bool {
        return $this->method === 'POST';
    }

    public static function normalizePath( string $path ): string {
        $path = '/' . trim($path, '/');
        return $path === '//' ? '/' : $path;
    }
}