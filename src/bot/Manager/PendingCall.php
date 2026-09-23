<?php

namespace natilosir\bot\Bot\Manager;

use ArrayAccess;
use JsonSerializable;
use LogicException;
use natilosir\bot\Bot\Contracts\BotDriver;
use Stringable;

final class PendingCall implements ArrayAccess, JsonSerializable, Stringable {
    public function __construct( private readonly BotDriver $driver, private readonly string $method, private readonly array $data, private readonly string $httpMethod = 'POST' ) {}

    private bool  $inspected = false;
    private bool  $sent      = false;
    private mixed $result    = null;

    public function url(): string {
        return rtrim($this->driver->baseUrl(), '/') . '/bot' . $this->driver->token() . '/' . ltrim($this->method, '/');
    }

    public function method(): string {
        return $this->method;
    }

    public function httpMethod(): string {
        return $this->httpMethod;
    }

    public function payload(): array {
        return [
            'driver'     => $this->driver->name(),
            'method'     => $this->method,
            'url'        => $this->url(),
            'httpMethod' => $this->httpMethod,
            'data'       => $this->data,
        ];
    }

    public function dd(): never {
        $this->inspected = true;
        dad($this->payload());
        exit;
    }

    public function dump(): static {
        $this->inspected = true;
        lg($this->payload());
        return $this;
    }

    public function send(): mixed {
        return $this->execute();
    }

    public function result(): mixed {
        return $this->execute();
    }

    public function __get( string $name ): mixed {
        $r = $this->execute();
        if ( is_array($r) ) return $r[$name] ?? null;
        if ( is_object($r) ) return $r->{$name} ?? null;
        return null;
    }

    public function __isset( string $name ): bool {
        $r = $this->execute();
        if ( is_array($r) ) return isset($r[$name]);
        if ( is_object($r) ) return isset($r->{$name});
        return false;
    }

    public function __call( string $name, array $arguments ): mixed {
        $r = $this->execute();
        if ( is_object($r) && is_callable([ $r, $name ]) ) {
            return $r->{$name}(...$arguments);
        }
        throw new LogicException("Method [{$name}] does not exist on PendingCall result.");
    }

    public function offsetExists( mixed $offset ): bool {
        $r = $this->execute();
        return is_array($r) && isset($r[$offset]);
    }

    public function offsetGet( mixed $offset ): mixed {
        $r = $this->execute();
        return is_array($r) ? ( $r[$offset] ?? null ) : null;
    }

    public function offsetSet( mixed $offset, mixed $value ): void {
        throw new LogicException('PendingCall result is read-only.');
    }

    public function offsetUnset( mixed $offset ): void {
        throw new LogicException('PendingCall result is read-only.');
    }

    public function jsonSerialize(): mixed {
        return $this->execute();
    }

    public function __toString(): string {
        $r = $this->execute();
        if ( is_string($r) ) return $r;
        return (string) json_encode($r, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function __debugInfo(): array {
        return $this->payload();
    }

    public function __destruct() {
        if ( !$this->inspected && !$this->sent ) {
            $this->execute();
        }
    }

    private function execute(): mixed {
        $this->inspected = true;
        if ( !$this->sent ) {
            $this->sent   = true;
            $this->result = $this->driver->api($this->method, $this->data, $this->httpMethod);
        }
        return $this->result;
    }
}