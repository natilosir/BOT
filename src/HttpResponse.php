<?php

namespace natilosir\bot;

class HttpResponse {
    public $status;
    public $body;

    private function recursiveObjectConvert($data) {
        if (is_array($data)) {
            $result = new \stdClass();
            foreach ($data as $key => $value) {
                $result->$key = $this->recursiveObjectConvert($value);
            }
            return $result;
        } elseif (is_object($data)) {
            foreach ($data as $key => $value) {
                $data->$key = $this->recursiveObjectConvert($value);
            }
            return $data;
        }
        return $data;
    }

    public function __construct($status, $body) {
        $this->status = $status;

        if (is_array($body)) {
            $body = $this->recursiveObjectConvert($body);
        } elseif (is_string($body)) {
            $decoded = json_decode($body, false);
            if (json_last_error() === JSON_ERROR_NONE) {
                $body = $decoded;
            } else {
                $body = new \stdClass();
                $body->raw = $body;
            }
        } elseif ($body === null) {
            $body = new \stdClass();
        }

        $this->body = $this->recursiveObjectConvert($body);

        $this->validateNoArrays($this->body);
    }
    private function validateNoArrays($data) {
        if (is_array($data)) {
            throw new \RuntimeException('Array found after conversion: ' . print_r($data, true));
        }
        if (is_object($data)) {
            foreach ($data as $value) {
                $this->validateNoArrays($value);
            }
        }
    }

    public function status() {
        return $this->status;
    }

    public function body() {
        return $this->body;
    }

    public function json() {
        return $this->body;
    }

    public function successful() {
        return $this->status >= 200 && $this->status < 300;
    }

    public function failed() {
        return !$this->successful();
    }

    public function dd() {
        return dd($this);
    }
}