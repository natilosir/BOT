<?php

use natilosir\bot\Log;

require_once __DIR__ . '/log.php';

if ( !function_exists('lg') ) {
    function lg( ...$data ): void {
        foreach ( $data as $d ) {
            Log::write('DEBUG', $d);
        }
    }
}

if ( !function_exists('log') ) {
    function log( ...$data ): void {
        foreach ( $data as $d ) {
            Log::write('DEBUG', $d);
        }
    }
}

if ( !function_exists('dad') ) {
    function dad( ...$data ): void {
        foreach ( $data as $d ) {
            Log::write('DEBUG', $d);
        }
    }
}

if ( !function_exists('dd') ) {
    function dd( ...$vars ): never {
        foreach ( $vars as $v ) {
            Log::debug($v);
        }
        die;
    }
}
