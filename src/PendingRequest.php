<?php

namespace Natilosir\Bot;

use Natilosir\Bot\Exceptions\ConnectionException;

class PendingRequest {
    protected string $baseUrl = '';
    protected array  $options = [];
    protected array  $headers = [];
    protected bool   $isJson  = false;

    public function baseUrl( string $url ): self {
        $this->baseUrl = $url;
        return $this;
    }

    public function acceptJson(): self {
        return $this->accept('application/json');
    }

    public function accept( string $contentType ): self {
        return $this->withHeaders([ 'Accept' => $contentType ]);
    }

    public function withHeaders( array $headers ): self {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function asJson(): self {
        $this->isJson = true;
        return $this->withHeaders([ 'Content-Type' => 'application/json' ]);
    }

    public function asForm(): self {
        $this->isJson = false;
        return $this->withHeaders([ 'Content-Type' => 'application/x-www-form-urlencoded' ]);
    }

    public function withToken( string $token, string $type = 'Bearer' ): self {
        return $this->withHeaders([ 'Authorization' => trim($type . ' ' . $token) ]);
    }

    public function withoutRedirecting(): self {
        return $this->withOptions([ CURLOPT_FOLLOWLOCATION => false ]);
    }

    public function withOptions( array $options ): self {
        $this->options = array_replace($this->options, $options);
        return $this;
    }

    public function withoutVerifying(): self {
        return $this->withOptions([
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
    }

    public function timeout( int $seconds ): self {
        return $this->withOptions([ CURLOPT_TIMEOUT => $seconds ]);
    }

    public function get( string $url, array $query = [] ): Response {
        return $this->send('GET', $url, [ 'query' => $query ]);
    }

    public function send( string $method, string $url, array $options = [] ): Response {
        $ch = curl_init();

        $url    = $this->buildUrl($url);
        $method = strtoupper($method);

        $curlOptions = $this->buildCurlOptions($method, $url, $options);
        curl_setopt_array($ch, $curlOptions);

        $responseBody = curl_exec($ch);
        $curlError    = curl_error($ch);

        if ( $curlError ) {
            curl_close($ch);
            throw new ConnectionException("cURL Error: " . $curlError);
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headerStr  = substr($responseBody, 0, $headerSize);
        $body       = substr($responseBody, $headerSize);

        curl_close($ch);

        $headers = $this->parseHeaders($headerStr);

        return new Response($body, $statusCode, $headers);
    }

    private function buildUrl( string $url ): string {
        if ( str_starts_with($url, 'http://') || str_starts_with($url, 'https://') ) {
            return $url;
        }
        return rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/');
    }

    private function buildCurlOptions( string $method, string &$url, array $options ): array {
        $curlOptions = $this->options;

        $curlOptions[CURLOPT_CUSTOMREQUEST]  = $method;
        $curlOptions[CURLOPT_RETURNTRANSFER] = true;
        $curlOptions[CURLOPT_HEADER]         = true;

        if ( !empty($options['query']) ) {
            $url .= '?' . http_build_query($options['query']);
        }

        if ( !empty($options['data']) ) {
            $curlOptions[CURLOPT_POSTFIELDS] = $options['data'];
        }

        $curlOptions[CURLOPT_URL] = $url;

        $headers = [];
        foreach ( $this->headers as $key => $value ) {
            $headers[] = $key . ': ' . $value;
        }
        if ( !isset($this->headers['Accept']) ) {
            $headers[] = 'Accept: application/json';
        }

        $curlOptions[CURLOPT_HTTPHEADER] = $headers;

        return $curlOptions;
    }

    private function parseHeaders( string $headerStr ): array {
        $headers     = [];
        $headerLines = explode("\r\n", trim($headerStr));
        array_shift($headerLines); // Remove HTTP status line
        foreach ( $headerLines as $line ) {
            $parts = explode(': ', $line, 2);
            if ( isset($parts[1]) ) {
                $headers[$parts[0]] = $parts[1];
            }
        }
        return $headers;
    }

    public function post( string $url, array $data = [] ): Response {
        return $this->send('POST', $url, [ 'data' => $data ]);
    }

    public function put( string $url, array $data = [] ): Response {
        return $this->send('PUT', $url, [ 'data' => $data ]);
    }

    public function patch( string $url, array $data = [] ): Response {
        return $this->send('PATCH', $url, [ 'data' => $data ]);
    }

    public function delete( string $url, array $data = [] ): Response {
        return $this->send('DELETE', $url, [ 'data' => $data ]);
    }
}
