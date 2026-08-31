<?php

require_once __DIR__ . '/log.php';

if (!function_exists('lg')) {
    function lg($data, string $level = 'DEBUG', array $context = []): void
    {
        \natilosir\bot\lg($data, $level, $context);
    }
}

if (!function_exists('dd')) {
    function dd(...$vars): never
    {
        \natilosir\bot\dd(...$vars);
    }
}
