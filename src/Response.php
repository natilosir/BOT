<?php

namespace Natilosir\Bot;

use Natilosir\Bot\Exceptions\RequestException;

class Response {
    protected string $body;
    protected int    $statusCode;
    protected array  $headers;
    protected        $decoded = null;
    protected array  $debugInfo;

    public function __construct( string $body, int $statusCode, array $headers, array $debugInfo = [] ) {
        $this->body       = $body;
        $this->statusCode = $statusCode;
        $this->headers    = $headers;
        $this->debugInfo  = $debugInfo;
    }

    public function header( string $key ): ?string {
        return $this->headers[$key] ?? null;
    }

    public function clientError(): bool {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    public function serverError(): bool {
        return $this->statusCode >= 500;
    }

    public function throw(): self {
        if ( $this->failed() ) {
            throw new RequestException($this);
        }
        return $this;
    }

    public function failed(): bool {
        return !$this->successful();
    }

    public function successful(): bool {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function __get( $key ) {
        return $this->object()->{$key} ?? null;
    }

    public function object() {
        return json_decode($this->body, false);
    }

    public function log() {
        $this->lg();
    }

    public function lg() {
        $payload = [
            'response' => [
                'body'    => $this->object(),
                'status'  => $this->status(),
                'headers' => $this->headers(),
            ],
            'request'  => [
                'method'  => $this->debugInfo['method'] ?? null,
                'url'     => $this->debugInfo['url'] ?? null,
                'headers' => $this->debugInfo['headers'] ?? null,
                'options' => $this->debugInfo['options'] ?? null,
                'is_json' => $this->debugInfo['is_json'] ?? false,
            ],
        ];
        Log::debug($payload);
    }

    public function status(): int {
        return $this->statusCode;
    }

    public function headers(): array {
        return $this->headers;
    }

    public function dd() {
        $this->lg();
        die();
    }

    public function body(): string {
        return $this->body;
    }

    public function array(): array {
        if ( $this->decoded === null ) {
            $this->decoded = json_decode($this->body, true);
        }
        return $this->decoded ?? [];
    }

    public function json( string $key = null, $default = null ) {
        if ( $this->decoded === null ) {
            $this->decoded = json_decode($this->body, true);
        }

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return $default;
        }

        if ( $key === null ) {
            return $this->decoded;
        }

        return $this->decoded[$key] ?? $default;
    }
}
