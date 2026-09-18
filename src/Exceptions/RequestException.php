<?php

namespace natilosir\bot\Exceptions;

use Exception;
use natilosir\bot\Response;

class RequestException extends Exception {
    public function __construct( Response $response ) {
        $message = "HTTP request returned status code {$response->status()}.";
        parent::__construct($message);
        $this->response = $response;
    }

    public Response $response;
}
