<?php

namespace natilosir\bot\Bot\Drivers;

use Illuminate\Http\Client\Factory;
use Illuminate\Support\Arr;
use natilosir\bot\Bot\Contracts\BotDriver;
use natilosir\bot\Bot\Contracts\WebhookAwareDriver;
use natilosir\bot\Bot\Webhook\WebhookRequest;
use natilosir\bot\Exceptions\RequestException;
use natilosir\bot\http\PendingRequest;

abstract class AbstractBotDriver implements BotDriver, WebhookAwareDriver {
    public function __construct( protected Factory $http, protected string $botToken, protected string $apiBaseUrl, protected array $config = [] ) {
        $this->apiBaseUrl = rtrim($this->apiBaseUrl, '/');
    }

    protected array $supportedMethods = [];

    // ---------- Capture state ----------
    private bool   $captureMode  = false;
    private ?array $capturedCall = null;

    public function api( string $method, array $data = [], string $httpMethod = 'POST' ): mixed {
        if ( $this->captureMode ) {
            $this->capturedCall = [
                'method'     => $method,
                'data'       => $data,
                'httpMethod' => $httpMethod,
            ];
            return null;
        }

        return $this->requestTo($this->methodUrl($method), $data, $httpMethod);
    }

    public function baseUrl(): string {
        return $this->apiBaseUrl;
    }

    public function beginCapture(): void {
        $this->captureMode  = true;
        $this->capturedCall = null;
    }

    public function endCapture(): ?array {
        $this->captureMode  = false;
        $call               = $this->capturedCall;
        $this->capturedCall = null;
        return $call;
    }

    public function supports( string $method ): bool {
        if ( $this->supportedMethods === [] ) {
            return true;
        }

        $needle = strtolower($method);
        foreach ( $this->supportedMethods as $supportedMethod ) {
            if ( strtolower($supportedMethod) === $needle ) {
                return true;
            }
        }

        return false;
    }

    public function token(): string {
        return $this->botToken;
    }

    protected function requestTo( string $url, array $data = [], string $httpMethod = 'POST' ): mixed {
        $request        = new PendingRequest($this->http);
        $hasAttachments = false;

        foreach ( $data as $key => $value ) {
            if ( $this->isFileDescriptor($value) ) {
                $path = (string) $value['tmp_name'];
                if ( !is_file($path) || !is_readable($path) ) {
                    throw new RequestException("Upload file is not readable: {$path}");
                }

                $stream = fopen($path, 'r');
                if ( $stream === false ) {
                    throw new RequestException("Could not open upload file: {$path}");
                }

                $request = $request->attach((string) $key, $stream, (string) ( $value['name'] ?? basename($path) ));
                unset($data[$key]);
                $hasAttachments = true;
            }
        }

        if ( $hasAttachments ) {
            foreach ( $data as $key => $value ) {
                if ( is_array($value) || is_object($value) ) {
                    $data[$key] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
        }
//        dd($url, $data);
        try {
            $response = match ( $httpMethod ) {
                'GET'    => $request->get($url, $data),
                'POST'   => $request->post($url, $data),
                'PUT'    => $request->put($url, $data),
                'PATCH'  => $request->patch($url, $data),
                'DELETE' => $request->delete($url, $data),
                default  => throw new RequestException("Unknown HTTP method: {$httpMethod}"),
            };
        } catch ( \Throwable $e ) {
            dd($e);
        }

        return $response->json();
    }

    public function config( ?string $key = null, mixed $default = null ): mixed {
        if ( $key === null || $key === '' ) {
            return $this->config;
        }

        $value = $this->config;
        foreach ( explode('.', $key) as $segment ) {
            if ( !is_array($value) || !array_key_exists($segment, $value) ) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function matchesWebhook( WebhookRequest $request ): bool {
        $webhook = $this->config['webhook'] ?? [];
        if ( !is_array($webhook) ) {
            return false;
        }

        $configuredUrl  = trim((string) ( $webhook['url'] ?? '' ));
        $configuredPath = trim((string) ( $webhook['path'] ?? '' ));

        if ( $configuredPath === '' && $configuredUrl !== '' ) {
            $configuredPath = (string) ( parse_url($configuredUrl, PHP_URL_PATH) ? : '/' );
        }

        if ( $configuredPath === '' ) {
            return false;
        }

        if ( WebhookRequest::normalizePath($configuredPath) !== $request->path() ) {
            return false;
        }

        $headerName  = trim((string) ( $webhook['header'] ?? '' ));
        $headerValue = (string) ( $webhook['header_value'] ?? '' );

        if ( $headerName !== '' || $headerValue !== '' ) {
            if ( $headerName === '' || $headerValue === '' ) {
                return false;
            }

            $received = $request->header($headerName);
            if ( $received === null || !hash_equals($headerValue, $received) ) {
                return false;
            }
        }

        return true;
    }

    protected function methodUrl( string $method ): string {
        return $this->apiBaseUrl . '/bot' . $this->botToken . '/' . ltrim($method, '/');
    }

    protected function isFileDescriptor( mixed $value ): bool {
        return is_array($value)
               && isset($value['tmp_name'])
               && is_string($value['tmp_name'])
               && $value['tmp_name'] !== '';
    }

    protected function dataFromFirst( array $args ): array {
        return isset($args[0]) && is_array($args[0]) ? $args[0] : [];
    }

    protected function addOptional( array &$data, string $key, mixed $value ): void {
        Arr::set($data, $key, $value);
    }
}