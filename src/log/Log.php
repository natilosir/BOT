<?php

namespace natilosir\bot\log;

class Log {
    public static function info( $data, array $context = [] ): void {
        self::write('INFO', $data, $context);
    }

    public static function write( string $level, $data, array $context = [] ): void {
        $bt     = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);
        $caller = [];
        foreach ( $bt as $frame ) {
            $class = $frame['class'] ?? '';
            if ( $class === 'natilosir\\bot\\Log' || strpos($class, 'AdvancedLogger') !== false ) {
                continue;
            }
            if ( isset($frame['file']) ) {
                $caller = $frame;
                break;
            }
        }
        $self = $bt[1] ?? [];

        if ( isset($self['class']) && isset($caller['class'])
             && $self['class'] === 'natilosir\\bot\\Log'
             && $caller['class'] === 'natilosir\\bot\\Log' ) {
            $caller = $bt[3] ?? $caller;
        }

        AdvancedLogger::getInstance()
            ->log($data, $level, $context, $caller['file'] ?? null, $caller['line'] ?? null, null, $caller['class'] ?? null, $caller['function'] ?? null);
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
}