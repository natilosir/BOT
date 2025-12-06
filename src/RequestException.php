<?php

namespace Natilosir\Bot\Exceptions;

use Natilosir\Bot\Response;

class RequestException extends \Exception {
    public Response $response;

    public function __construct( Response $response ) {
        $message = "HTTP request returned status code {$response->status()}.";
        parent::__construct($message);
        $this->response = $response;
    }
}
