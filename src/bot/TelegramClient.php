<?php

namespace natilosir\bot\bot;

use Illuminate\Http\Client\Factory;
use InvalidArgumentException;
use natilosir\bot\http\PendingRequest;

class TelegramClient {
    public function __construct( protected Factory $http ) {}

    public function api( string $method, array $data = [], string $httpMethod = 'POST' ): array {
        $url = 'https://api.telegram.org/bot' . paths()->config('bot.token') . '/' . ltrim($method, '/');

        $request = new PendingRequest($this->http);
        foreach ( $data as $key => $value ) {
            if ( is_array($value) && isset($value['tmp_name']) ) {
                $request = $request->attach($key, fopen($value['tmp_name'], 'r'), $value['name'] ?? basename($value['tmp_name']));
                unset($data[$key]);
            }
        }

        $response = match ( strtoupper($httpMethod) ) {
            'GET'    => $request->get($url, $data),
            'POST'   => $request->post($url, $data),
            'PUT'    => $request->put($url, $data),
            'PATCH'  => $request->patch($url, $data),
            'DELETE' => $request->delete($url, $data),
            default  => throw new InvalidArgumentException("Unknown HTTP method")
        };

        $json = $response->json();

        if ( !is_array($json) ) {
            $json = $json === null ? [] : (array) $json;
        }

        return $json;
    }
}
