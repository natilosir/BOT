<?php

namespace natilosir\bot\Bot\Drivers;

use Closure;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Arr;
use natilosir\bot\Bot\Contracts\BotDriver;
use natilosir\bot\Bot\Contracts\WebhookAwareDriver;
use natilosir\bot\Bot\Transport\HttpBotTransport;
use natilosir\bot\Bot\Webhook\WebhookRequest;
use natilosir\bot\Exceptions\RequestException;

abstract class AbstractBotDriver implements BotDriver, WebhookAwareDriver {
    public function __construct( protected Factory $http, protected string $botToken, protected string $apiBaseUrl, protected array $config = [], ?HttpBotTransport $transport = null ) {
        $this->apiBaseUrl = rtrim($this->apiBaseUrl, '/');
        $this->transport  = $transport ?? new HttpBotTransport($this->http);
        RequestException::registerToken($this->botToken);
    }

    protected array          $supportedMethods      = [];
    private bool             $captureMode           = false;
    private ?array           $capturedCall          = null;
    private ?array           $supportedMethodLookup = null;
    private HttpBotTransport $transport;

    public function api( string $method, array $data = [], string $httpMethod = 'POST' ): mixed {
        $httpMethod = strtoupper($httpMethod);

        if ( $this->captureMode ) {
            $url = $this->methodUrl($method);

            return $this->captureCall($method, $data, $httpMethod, $url, fn(): mixed => $this->api($method, $data, $httpMethod));
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
        $capturedCall       = $this->capturedCall;
        $this->captureMode  = false;
        $this->capturedCall = null;

        return $capturedCall;
    }

    public function supports( string $method ): bool {
        if ( $this->supportedMethods === [] ) {
            return true;
        }

        $this->supportedMethodLookup ??= array_fill_keys(array_map('strtolower', $this->supportedMethods), true);

        return isset($this->supportedMethodLookup[strtolower($method)]);
    }

    public function token(): string {
        return $this->botToken;
    }

    public function config( ?string $key = null, mixed $default = null ): mixed {
        if ( $key === null || $key === '' ) {
            return $this->config;
        }

        return Arr::get($this->config, $key, $default);
    }

    public function matchesWebhook( WebhookRequest $request ): bool {
        $webhook = $this->config('webhook', []);
        if ( !is_array($webhook) ) {
            return false;
        }

        $configuredUrl  = trim((string) Arr::get($webhook, 'url', ''));
        $configuredPath = trim((string) Arr::get($webhook, 'path', ''));

        if ( $configuredPath === '' && $configuredUrl !== '' ) {
            $configuredPath = (string) ( parse_url($configuredUrl, PHP_URL_PATH) ? : '/' );
        }

        if ( $configuredPath === '' || WebhookRequest::normalizePath($configuredPath) !== $request->path() ) {
            return false;
        }

        $headerName  = trim((string) Arr::get($webhook, 'header', ''));
        $headerValue = (string) Arr::get($webhook, 'header_value', '');

        if ( $headerName === '' && $headerValue === '' ) {
            return true;
        }

        if ( $headerName === '' || $headerValue === '' ) {
            return false;
        }

        $received = $request->header($headerName);

        return $received !== null && hash_equals($headerValue, $received);
    }

    protected function requestTo( string $url, array $data = [], string $httpMethod = 'POST' ): mixed {
        $httpMethod = strtoupper($httpMethod);

        if ( $this->captureMode ) {
            $method = basename((string) parse_url($url, PHP_URL_PATH));

            return $this->captureCall($method, $data, $httpMethod, $url, fn(): mixed => $this->requestTo($url, $data, $httpMethod));
        }

        return $this->transport->send($url, $data, $httpMethod);
    }

    protected function captureCall( string $method, array $data, string $httpMethod, string $url, Closure $executor ): mixed {
        $this->capturedCall = [
            'method'     => $method,
            'data'       => $data,
            'httpMethod' => strtoupper($httpMethod),
            'url'        => $url,
            'executor'   => $executor,
        ];

        return null;
    }

    protected function methodUrl( string $method ): string {
        return $this->apiBaseUrl . '/bot' . $this->botToken . '/' . ltrim($method, '/');
    }

}
