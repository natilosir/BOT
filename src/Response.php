<?php

namespace natilosir\bot;

use Illuminate\Http\Client\Response as IlluminateResponse;

class Response
{
    public function __construct(private IlluminateResponse $response)
    {
    }

    public function __call(string $method, array $parameters): mixed
    {
        return $this->response->{$method}(...$parameters);
    }

    public function __get(string $key): mixed
    {
        $object = $this->object();
        return is_object($object) ? ($object->{$key} ?? null) : null;
    }

    public function body(): string
    {
        return $this->response->body();
    }

    public function json(?string $key = null, mixed $default = null): mixed
    {
        return $this->response->json($key, $default);
    }

    public function array(): array
    {
        $json = $this->response->json();
        return is_array($json) ? $json : [];
    }

    public function object(): mixed
    {
        return $this->response->object();
    }

    public function status(): int
    {
        return $this->response->status();
    }

    public function headers(): array
    {
        return $this->response->headers();
    }

    public function header(string $key): ?string
    {
        return $this->response->header($key);
    }

    public function successful(): bool
    {
        return $this->response->successful();
    }

    public function failed(): bool
    {
        return $this->response->failed();
    }

    public function clientError(): bool
    {
        return $this->response->clientError();
    }

    public function serverError(): bool
    {
        return $this->response->serverError();
    }

    public function throw(): static
    {
        $this->response->throw();
        return $this;
    }

    public function lg(): static
    {
        lg([
            'response' => [
                'status' => $this->status(),
                'headers' => $this->headers(),
                'body' => $this->object() ?? $this->body(),
            ],
        ]);
        return $this;
    }

    public function log(): static
    {
        return $this->lg();
    }

    public function dd(): never
    {
        dd([
            'status' => $this->status(),
            'headers' => $this->headers(),
            'body' => $this->object() ?? $this->body(),
        ]);
    }
}
