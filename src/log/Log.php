<?php

namespace natilosir\bot\log;

class Log {
    public static function info( $data, array $context = [] ): void {
        self::write('INFO', $data, $context);
    }

    public static function write( string $level, $data, array $context = [] ): void {
        $bt = debug_backtrace();

        $lastInternal  = null;
        $firstExternal = null;

        foreach ( $bt as $frame ) {
            if ( self::isInternalFrame($frame) ) {
                $lastInternal = $frame;
            }
            else {
                $firstExternal = $frame;
                break;
            }
        }

        $callerFile = $lastInternal['file'] ?? $firstExternal['file'] ?? null;
        $callerLine = $lastInternal['line'] ?? $firstExternal['line'] ?? null;

        $callerClass    = $firstExternal['class'] ?? null;
        $callerFunction = $firstExternal['function'] ?? null;

        AdvancedLogger::getInstance()
            ->log($data, $level, $context, $callerFile, $callerLine, $bt, $callerClass, $callerFunction);
    }

    public static function debug( $data, array $context = [] ): void {
        self::write('DEBUG', $data, $context);
    }

    public static function error( $data, array $context = [] ): void {
        self::write('ERROR', $data, $context);
    }

    public static function warning( $data, array $context = [] ): void {
        self::write('WARNING', $data, $context);
    }

    public static function notice( $data, array $context = [] ): void {
        self::write('NOTICE', $data, $context);
    }

    private static function isInternalFrame( array $frame ): bool {
        $class = $frame['class'] ?? '';
        $func  = strtolower($frame['function'] ?? '');

        if ( stripos($class, 'AdvancedLogger') !== false ) return true;
        if ( stripos($class, 'natilosir\\bot\\log\\') !== false ) return true;

        if ( in_array($func, [ 'lg', 'log', 'dad', 'dd' ], true) ) return true;

        return false;
    }
}