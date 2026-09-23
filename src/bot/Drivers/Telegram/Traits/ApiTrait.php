<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait ApiTrait {
    public function request( string $method, array $data = [], string $httpMethod = 'POST' ): mixed {
        return $this->api($method, $data, $httpMethod);
    }

    public function file( string $path, ?string $name = null ): array {
        return [
            'tmp_name' => $path,
            'name'     => $name ? : basename($path),
        ];
    }

    protected function withExtra( array $data, array $extra = [] ): array {
        return $extra ? array_merge($data, $extra) : $data;
    }

    public function __call( string $name, array $arguments ): mixed {
        $data       = isset($arguments[0]) && is_array($arguments[0]) ? $arguments[0] : [];
        $httpMethod = isset($arguments[1]) && is_string($arguments[1]) ? $arguments[1] : 'POST';

        return $this->api($name, $data, $httpMethod);
    }
}
