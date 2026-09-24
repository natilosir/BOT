<?php

namespace natilosir\bot\Bot\Manager;

use ArrayAccess;
use Closure;
use JsonSerializable;
use LogicException;
use natilosir\bot\Bot\Contracts\BotDriver;
use Stringable;
use Throwable;

/**
 * Lazy bot API call.
 *
 * Calls created through BotManager are captured first and executed only when
 * the result is requested. For backward compatibility, unresolved calls are
 * still auto-sent on destruction unless explicitly inspected with dump() or
 * withoutAutoSend().
 *
 * @implements ArrayAccess<string|int, mixed>
 */
final class PendingCall implements ArrayAccess, JsonSerializable, Stringable {
    public function __construct( private readonly BotDriver $driver, private readonly string $method, private readonly array $data, private readonly string $httpMethod = 'POST', private readonly ?Closure $executor = null, private readonly ?string $capturedUrl = null ) {}

    private bool       $resolved  = false;
    private bool       $executing = false;
    private bool       $autoSend  = true;
    private mixed      $result    = null;
    private ?Throwable $failure   = null;

    public function driver(): BotDriver {
        return $this->driver;
    }

    public function method(): string {
        return $this->method;
    }

    public function httpMethod(): string {
        return strtoupper($this->httpMethod);
    }

    public function data(): array {
        return $this->data;
    }

    public function url(): string {
        if ( $this->capturedUrl !== null ) {
            return $this->capturedUrl;
        }

        return rtrim($this->driver->baseUrl(), '/') . '/bot' . $this->driver->token() . '/' . ltrim($this->method, '/');
    }

    public function payload(): array {
        return [
            'driver'     => $this->driver->name(),
            'method'     => $this->method,
            'url'        => $this->url(),
            'httpMethod' => $this->httpMethod(),
            'data'       => $this->data,
        ];
    }

    public function isPending(): bool {
        return !$this->resolved && $this->failure === null;
    }

    public function isResolved(): bool {
        return $this->resolved;
    }

    public function hasFailed(): bool {
        return $this->failure !== null;
    }

    public function withoutAutoSend(): static {
        $this->autoSend = false;
        return $this;
    }

    public function dump(): static {
        $this->autoSend = false;
        lg($this->payload());

        return $this;
    }

    public function lg(): static {
        lg($this->payload(), $this->result());

        return $this;
    }

    public function dd(): never {
        $this->autoSend = false;
        dad($this->payload(), $this->result());
    }

    public function send(): mixed {
        return $this->resolve();
    }

    public function result(): mixed {
        return $this->resolve();
    }

    public function __get( string $name ): mixed {
        return $this->readResultValue($name);
    }

    public function __isset( string $name ): bool {
        return $this->hasResultValue($name);
    }

    public function __call( string $name, array $arguments ): mixed {
        $result = $this->resolve();

        if ( is_object($result) && is_callable([ $result, $name ]) ) {
            return $result->{$name}(...$arguments);
        }

        throw new LogicException("Method [{$name}] does not exist on PendingCall result.");
    }

    public function offsetExists( mixed $offset ): bool {
        return $this->hasResultValue($offset);
    }

    public function offsetGet( mixed $offset ): mixed {
        return $this->readResultValue($offset);
    }

    public function offsetSet( mixed $offset, mixed $value ): void {
        throw new LogicException('PendingCall result is read-only.');
    }

    public function offsetUnset( mixed $offset ): void {
        throw new LogicException('PendingCall result is read-only.');
    }

    public function jsonSerialize(): mixed {
        return $this->resolve();
    }

    public function __toString(): string {
        $result = $this->resolve();

        if ( is_string($result) ) {
            return $result;
        }

        return (string) json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    public function __debugInfo(): array {
        return [
            ...$this->payload(),
            'state'    => $this->failure !== null ? 'failed' : ( $this->resolved ? 'resolved' : 'pending' ),
            'autoSend' => $this->autoSend,
        ];
    }

    public function __destruct() {
        if ( !$this->autoSend || !$this->isPending() ) {
            return;
        }

        try {
            $this->resolve();
        } catch ( Throwable $e ) {
            // Throwing from a destructor can turn a recoverable API problem into
            // a fatal shutdown error. Keep backward-compatible auto-send while
            // making shutdown failure safe and observable in the debug log.
            try {
                lg([
                    'message' => 'Pending bot call failed during auto-send.',
                    'call'    => $this->payload(),
                    'error'   => $e->getMessage(),
                ]);
            } catch ( Throwable ) {
                // Logging must never cause another shutdown failure.
            }
        }
    }

    private function resolve(): mixed {
        $this->autoSend = false;

        if ( $this->resolved ) {
            return $this->result;
        }

        if ( $this->failure !== null ) {
            throw $this->failure;
        }

        if ( $this->executing ) {
            throw new LogicException('PendingCall cannot be executed recursively.');
        }

        $this->executing = true;

        try {
            $this->result = $this->executor !== null ? ( $this->executor )() : $this->driver->api($this->method, $this->data, $this->httpMethod());

            $this->resolved = true;

            return $this->result;
        } catch ( Throwable $e ) {
            $this->failure = $e;
            throw $e;
        } finally {
            $this->executing = false;
        }
    }

    private function readResultValue( mixed $key ): mixed {
        $result = $this->resolve();

        if ( is_array($result) ) {
            return $result[$key] ?? null;
        }

        if ( is_object($result) ) {
            return $result->{$key} ?? null;
        }

        return null;
    }

    private function hasResultValue( mixed $key ): bool {
        $result = $this->resolve();

        if ( is_array($result) ) {
            return isset($result[$key]);
        }

        if ( is_object($result) ) {
            return isset($result->{$key});
        }

        return false;
    }
}
