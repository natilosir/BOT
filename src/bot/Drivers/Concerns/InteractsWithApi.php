<?php

namespace natilosir\bot\Bot\Drivers\Concerns;

/**
 * Shared convenience layer for concrete bot drivers.
 *
 * Keeps transport concerns inside AbstractBotDriver while exposing the small
 * ergonomic API used by both Telegram and Bale drivers.
 */
trait InteractsWithApi {
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
        return $extra === [] ? $data : array_merge($data, $extra);
    }

    protected function buildApiData( array $arguments ): array {
        if ( isset($arguments[0]) && is_array($arguments[0]) ) {
            return $arguments[0];
        }

        $data = [];
        foreach ( $arguments as $index => $value ) {
            $data['param_' . $index] = $value;
        }

        return $data;
    }

    protected function apiFromArguments( string $method, array $arguments, string $httpMethod = 'POST' ): mixed {
        return $this->api($method, $this->buildApiData($arguments), $httpMethod);
    }

    public function __call( string $name, array $arguments ): mixed {
        $data       = isset($arguments[0]) && is_array($arguments[0]) ? $arguments[0] : [];
        $httpMethod = isset($arguments[1]) && is_string($arguments[1]) ? $arguments[1] : 'POST';

        return $this->api($name, $data, $httpMethod);
    }
}
