<?php

use Illuminate\Http\Client\ConnectionException;
use natilosir\bot\Http;

$config    = require paths()->config;
$BOT_TOKEN = $config['bot']['token'];

defined('api') || define('api', 'https://api.telegram.org/bot' . $BOT_TOKEN . '/');

function http( $uri, $data = [], $method = 'POST' ) {
    global $BOT_TOKEN;

    $method = strtoupper($method);
    $url    = str_starts_with($uri, 'http://') || str_starts_with($uri, 'https://') ? $uri : api . ltrim($uri, '/');

    $request = Http::withoutVerifying();

    foreach ( $data as $key => $value ) {
        if ( is_array($value) && isset($value['tmp_name']) && is_file($value['tmp_name']) ) {
            $request = $request->attach($key, fopen($value['tmp_name'], 'r'), $value['name'] ?? basename($value['tmp_name']));
            unset($data[$key]);
        }
    }

    try {
        $response = match ( $method ) {
            'GET'    => $request->get($url, $data),
            'POST'   => $request->post($url, $data),
            'PUT'    => $request->put($url, $data),
            'PATCH'  => $request->patch($url, $data),
            'DELETE' => $request->delete($url, $data),
            default  => throw new InvalidArgumentException("Unknown HTTP method: {$method}"),
        };

        return $response->json() ?? [];
    } catch ( ConnectionException $e ) {
        $msg = str_replace($BOT_TOKEN, '{BOT_TOKEN}', $e->getMessage());
        $msg = preg_replace('/bot\d+:[A-Za-z0-9_-]+/', 'bot{TOKEN}', $msg);
        throw new ConnectionException($msg);
    } catch ( \Throwable $e ) {
        $msg = str_replace($BOT_TOKEN, '{BOT_TOKEN}', $e->getMessage());
        $msg = preg_replace('/bot\d+:[A-Za-z0-9_-]+/', 'bot{TOKEN}', $msg);
        throw new \RuntimeException($msg, (int) $e->getCode());
    }
}