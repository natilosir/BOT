<?php

namespace natilosir\bot\Exceptions;

use natilosir\bot\log\Log;
use natilosir\bot\Response;

class RuntimeException extends \Exception {
    public function __construct( string $response ) {
//        Log::error($response);
        parent::__construct($response);
    }

}