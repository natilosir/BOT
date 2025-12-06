<?php

use Natilosir\Bot\Http;

$config    = require __DIR__ . '/../../../../config.php';
$BOT_TOKEN = $config['bot']['token'];
define('api', 'https://api.telegram.org/bot' . $BOT_TOKEN . '/');

function http($uri, $data = [], $method = 'POST')
{
    $method = strtoupper($method);

    // Build full URL
    if (str_starts_with($uri, 'http://') || str_starts_with($uri, 'https://')) {
        $base = '';
        $url  = $uri;
    } else {
        $base = api;
        $url  = ltrim($uri, '/');
    }

    foreach ($data as $key => $value) {
        if (is_array($value) && isset($value['tmp_name']) && is_file($value['tmp_name'])) {
            $data[$key] = new CURLFile(
                $value['tmp_name'],
                $value['type'] ?? null,
                $value['name'] ?? 'file'
            );
        }
    }

    $request = Http::baseUrl($base)->withoutVerifying();

    switch ($method) {
        case 'GET':
            $response = $request->get($url, $data);
            break;
        case 'POST':
            $response = $request->post($url, $data);
            break;
        case 'PUT':
            $response = $request->put($url, $data);
            break;
        case 'PATCH':
            $response = $request->patch($url, $data);
            break;
        case 'DELETE':
            $response = $request->delete($url, $data);
            break;
        default:
            throw new Exception("Unknown HTTP method: $method");
    }

    return $response->array();
}
