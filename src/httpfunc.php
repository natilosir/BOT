<?php

use natilosir\bot\Http;

$config = require __DIR__ . '/../../../../config.php';
$BOT_TOKEN = $config['bot']['token'];

defined('api') || define('api', 'https://api.telegram.org/bot' . $BOT_TOKEN . '/');

function http($uri, $data = [], $method = 'POST')
{
    $method = strtoupper($method);
    $url = str_starts_with($uri, 'http://') || str_starts_with($uri, 'https://')
        ? $uri
        : api . ltrim($uri, '/');

    $request = Http::withoutVerifying();

    // Illuminate HTTP handles multipart uploads via attach().
    foreach ($data as $key => $value) {
        if (is_array($value) && isset($value['tmp_name']) && is_file($value['tmp_name'])) {
            $request = $request->attach(
                $key,
                fopen($value['tmp_name'], 'r'),
                $value['name'] ?? basename($value['tmp_name'])
            );
            unset($data[$key]);
        }
    }

    $response = match ($method) {
        'GET' => $request->get($url, $data),
        'POST' => $request->post($url, $data),
        'PUT' => $request->put($url, $data),
        'PATCH' => $request->patch($url, $data),
        'DELETE' => $request->delete($url, $data),
        default => throw new InvalidArgumentException("Unknown HTTP method: {$method}"),
    };

    return $response->json() ?? [];
}
