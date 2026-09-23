<?php

namespace natilosir\bot\Exceptions;

use natilosir\bot\log\Log;

class ConnectionException extends \Exception {
    public function __construct( string $response ) {
//        Log::error($message);
        parent::__construct($message);
    }
}