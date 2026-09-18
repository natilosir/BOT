<?php

namespace natilosir\bot\bot\Traits;

trait ApiTrait {
    public function api( string $method, array $data = [], string $httpMethod = 'POST' ) {
        return $this->client()
            ->api($method, $data, $httpMethod);
    }

    public function request( string $method, array $data = [], string $httpMethod = 'POST' ) {
        return $this->api($method, $data, $httpMethod);
    }

    public function file( string $path, ?string $name = null ): array {
        return [ 'tmp_name' => $path, 'name' => $name ? : basename($path) ];
    }

    protected function addOptional( array &$data, string $key, $value ): void {
        if ( $value !== null ) $data[$key] = $value;
    }

    protected function withExtra( array $data, array $extra = [] ): array {
        return $extra ? array_merge($data, $extra) : $data;
    }

    public function __call( string $name, array $arguments ) {
        return $this->api($name, $arguments[0] ?? [], $arguments[1] ?? 'POST');
    }
}
