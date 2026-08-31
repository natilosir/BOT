<?php

namespace natilosir\bot;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest as IlluminatePendingRequest;

class PendingRequest
{
    private IlluminatePendingRequest $request;

    public function __construct(?Factory $factory = null)
    {
        $factory ??= new Factory();
        $this->request = $factory->createPendingRequest();
    }

    public function __call(string $method, array $parameters): mixed
    {
        $result = $this->request->{$method}(...$parameters);

        if ($result instanceof IlluminatePendingRequest) {
            $this->request = $result;
            return $this;
        }

        if ($result instanceof \Illuminate\Http\Client\Response) {
            return new Response($result);
        }

        return $result;
    }

    public function get(string $url, array|string|null $query = null): Response
    {
        return new Response($this->request->get($url, $query));
    }

    public function post(string $url, array $data = []): Response
    {
        return new Response($this->request->post($url, $data));
    }

    public function put(string $url, array $data = []): Response
    {
        return new Response($this->request->put($url, $data));
    }

    public function patch(string $url, array $data = []): Response
    {
        return new Response($this->request->patch($url, $data));
    }

    public function delete(string $url, array $data = []): Response
    {
        return new Response($this->request->delete($url, $data));
    }

    public function send(string $method, string $url, array $options = []): Response
    {
        return new Response($this->request->send($method, $url, $options));
    }
}
