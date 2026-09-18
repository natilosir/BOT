<?php

use Illuminate\Http\Client\ConnectionException;
use natilosir\bot\Http;

/**
 * Resolve Telegram configuration lazily.
 *
 * helpers.php is loaded by Composer very early. Reading paths()->config()
 * while Composer is still bootstrapping can happen before Bootstrap is ready,
 * so the token is resolved only when the first Telegram request is made.
 */
function http( $uri, $data = [], $method = 'POST' ) {
    static $botToken = null;

    if ( $botToken === null ) {
        $botToken             = paths()->config('bot.token');
        $GLOBALS['BOT_TOKEN'] = $botToken;

        if ( !defined('api') ) {
            define('api', 'https://api.telegram.org/bot' . $botToken . '/');
        }
    }

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
        $msg = str_replace($botToken, '{BOT_TOKEN}', $e->getMessage());
        $msg = preg_replace('/bot\d+:[A-Za-z0-9_-]+/', 'bot{TOKEN}', $msg);
        throw new ConnectionException($msg);
    } catch ( Throwable $e ) {
        $msg = str_replace($botToken, '{BOT_TOKEN}', $e->getMessage());
        $msg = preg_replace('/bot\d+:[A-Za-z0-9_-]+/', 'bot{TOKEN}', $msg);
        throw new RuntimeException($msg, (int) $e->getCode());
    }
}
