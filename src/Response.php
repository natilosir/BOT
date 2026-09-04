<?php

namespace natilosir\bot;

use Illuminate\Http\Client\Response as IlluminateResponse;
use JsonSerializable;
use stdClass;

class Response implements JsonSerializable {
    public function __construct( IlluminateResponse $response ) {
        $this->response = $response;
    }

    private IlluminateResponse $response;

    public function __get( string $key ): mixed {
        $data = $this->object();
        return is_object($data) ? ( $data->{$key} ?? null ) : null;
    }

    public function __call( string $method, array $parameters ): mixed {
        return $this->response->{$method}(...$parameters);
    }

    public function __debugInfo(): array {
        $data = $this->object();
        return is_object($data) ? (array) $data : [ 'data' => $data ];
    }

    public function jsonSerialize(): mixed {
        return $this->object();
    }

    public function __toString(): string {
        return json_encode($this->object(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function body(): string {
        return $this->response->body();
    }

    public function json( ?string $key = null, mixed $default = null ): mixed {
        return $this->convertToDeepObject($this->response->json($key, $default));
    }

    public function array(): mixed {
        return $this->convertToDeepObject($this->response->json());
    }

    public function object(): mixed {
        return $this->convertToDeepObject($this->response->object());
    }

    public function status(): int {
        return $this->response->status();
    }

    public function headers(): array {
        return $this->response->headers();
    }

    public function header( string $key ): ?string {
        return $this->response->header($key);
    }

    public function successful(): bool {
        return $this->response->successful();
    }

    public function failed(): bool {
        return $this->response->failed();
    }

    public function clientError(): bool {
        return $this->response->clientError();
    }

    public function serverError(): bool {
        return $this->response->serverError();
    }

    public function throw(): static {
        $this->response->throw();
        return $this;
    }

    public function lg(): static {
        lg([
            'status' => $this->status(),
            'data'   => $this->object(),
        ]);
        return $this;
    }

    public function log(): static {
        return $this->lg();
    }

    public function dd(): never {
        dd($this->object());
    }

    private function convertToDeepObject( mixed $data ): mixed {
        if ( is_array($data) ) {
            $obj = new stdClass();
            foreach ( $data as $key => $value ) {
                $obj->{$key} = $this->convertToDeepObject($value);
            }
            return $obj;
        }

        if ( is_object($data) && get_class($data) === stdClass::class ) {
            foreach ( $data as $key => $value ) {
                $data->{$key} = $this->convertToDeepObject($value);
            }
            return $data;
        }

        return $data;
    }
}