<?php

namespace natilosir\bot\Exceptions;

use natilosir\bot\log\Log;
use natilosir\bot\Response;

class RequestException extends \Exception {
    public function __construct( Response|string $response, int $code = 0, ?\Throwable $previous = null ) {
        if ( $response instanceof Response ) {
            $message        = "HTTP request returned status code {$response->status()}.";
            $this->response = $response;
        }
        else {
            $message        = $response;
            $this->response = null;
        }
//        Log::error($message);

        parent::__construct($message, $code, $previous);
    }

    public ?Response $response;
}