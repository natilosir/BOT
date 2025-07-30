<?php

namespace natilosir\bot;

class Http {
    protected $baseUrl;
    protected $headers = [];
    protected $options = [];

    public function __construct($baseUrl = null) {
        $this->baseUrl = $baseUrl;
    }

    public static function new() {
        return new self();
    }

    public static function post($url, array $data = []) {
        return (new self())->send('POST', $url, [
            'form_params' => $data,
        ]);
    }

    public static function get(string $url, array $query = []) {
        return (new self())->send('GET', $url, [
            'query' => $query,
        ]);
    }

    public static function put(string $url, array $data = []) {
        return (new self())->send('PUT', $url, [
            'form_params' => $data,
        ]);
    }

    public static function patch(string $url, array $data = []) {
        return (new self())->send('PATCH', $url, [
            'form_params' => $data,
        ]);
    }

    public static function delete(string $url, array $data = []) {
        return (new self())->send('DELETE', $url, [
            'form_params' => $data,
        ]);
    }

    public function baseUrl(string $url) {
        $this->baseUrl = $url;
        return $this;
    }

    public function withHeaders(array $headers) {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function withOptions(array $options) {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function send(string $method, string $url, array $options = []) {
        $ch = curl_init();

        $fullUrl = $this->buildUrl($url);

        $method = strtoupper($method);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($method === 'GET' && !empty($options['query'])) {
            $fullUrl .= '?' . http_build_query($options['query']);
        }

        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE']) && !empty($options['form_params'])) {
            // برای درخواست‌های JSON، داده‌ها به صورت JSON ارسال شوند
            if (isset($this->headers['Content-Type']) && $this->headers['Content-Type'] === 'application/json') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($options['form_params']));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($options['form_params']));
            }
        }

        curl_setopt($ch, CURLOPT_URL, $fullUrl);

        // اضافه کردن هدر Accept برای اطمینان از دریافت JSON
        $headers = array_merge(['Accept: application/json'], $this->headers);
        $curlHeaders = [];
        foreach ($headers as $key => $value) {
            $curlHeaders[] = "$key: $value";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        foreach ($this->options as $option => $value) {
            curl_setopt($ch, $option, $value);
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($error) {
            throw new \Exception("HTTP request failed: " . $error);
        }

        $decoded = is_string($response) ? json_decode($response, false) : $response;
        if (json_last_error() === JSON_ERROR_NONE) {
            $response = $decoded;
        } else {
            $response = $response ?: new \stdClass();
        }

        return new HttpResponse($statusCode, $response);
    }

    protected function buildUrl(string $url) {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        return rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/');
    }
}