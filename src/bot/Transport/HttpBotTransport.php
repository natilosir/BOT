<?php

namespace natilosir\bot\Bot\Transport;

use Illuminate\Http\Client\Factory;
use natilosir\bot\Exceptions\RequestException;
use natilosir\bot\http\PendingRequest;
use Throwable;

/**
 * HTTP transport shared by bot drivers.
 *
 * Drivers only describe API URLs/payloads. Multipart handling, stream lifetime,
 * HTTP dispatch and transport-level exception normalization live here.
 */
final class HttpBotTransport {
    public function __construct( private readonly Factory $http ) {}

    public function send( string $url, array $data = [], string $httpMethod = 'POST' ): mixed {
        $request = new PendingRequest($this->http);
        $streams = [];

        try {
            [ $request, $data, $streams ] = $this->prepareRequest($request, $data);
            $response = $this->dispatch($request, $url, $data, strtoupper($httpMethod));

            return $response->json();
        } catch ( Throwable $e ) {
            if ( $e instanceof RequestException ) {
                throw $e;
            }

            throw new RequestException(sprintf('Bot API request failed [%s]: %s', strtoupper($httpMethod), $e->getMessage()), (int) $e->getCode(), $e,);
        } finally {
            foreach ( $streams as $stream ) {
                if ( is_resource($stream) ) {
                    fclose($stream);
                }
            }
        }
    }

    /**
     * @return array{0: PendingRequest, 1: array, 2: array<int, resource>}
     */
    private function prepareRequest( PendingRequest $request, array $data ): array {
        $streams        = [];
        $hasAttachments = false;

        foreach ( $data as $key => $value ) {
            if ( !$this->isFileDescriptor($value) ) {
                continue;
            }

            $path = (string) $value['tmp_name'];
            if ( !is_file($path) || !is_readable($path) ) {
                throw new RequestException("Upload file is not readable: {$path}");
            }

            $stream = fopen($path, 'r');
            if ( $stream === false ) {
                throw new RequestException("Could not open upload file: {$path}");
            }

            $streams[] = $stream;
            $request   = $request->attach((string) $key, $stream, (string) ( $value['name'] ?? basename($path) ));

            unset($data[$key]);
            $hasAttachments = true;
        }

        if ( $hasAttachments ) {
            foreach ( $data as $key => $value ) {
                if ( is_array($value) || is_object($value) ) {
                    $data[$key] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
            }
        }

        return [ $request, $data, $streams ];
    }

    private function dispatch( PendingRequest $request, string $url, array $data, string $httpMethod ): mixed {
        return match ( $httpMethod ) {
            'GET'    => $request->get($url, $data),
            'POST'   => $request->post($url, $data),
            'PUT'    => $request->put($url, $data),
            'PATCH'  => $request->patch($url, $data),
            'DELETE' => $request->delete($url, $data),
            default  => throw new RequestException("Unknown HTTP method: {$httpMethod}"),
        };
    }

    private function isFileDescriptor( mixed $value ): bool {
        return is_array($value)
               && isset($value['tmp_name'])
               && is_string($value['tmp_name'])
               && $value['tmp_name'] !== '';
    }
}
