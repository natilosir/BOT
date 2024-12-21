<?php

function setupErrorHandling()
{
    $logFilePath = __DIR__.'/error.txt';
    if (file_exists($logFilePath)) {
        unlink($logFilePath);
    }

    ini_set('error_reporting', E_ALL & ~E_NOTICE);
    ini_set('display_errors', 'On');
    ini_set('log_errors', 'On');
    ini_set('error_log', $logFilePath);
}

function processLogFile()
{
    $logFilePath = __DIR__.'/error.txt';

    if (file_exists($logFilePath)) {
        $content        = file_get_contents($logFilePath);
        $lines          = explode("\n", $content);
        $processedLines = [];

        foreach ($lines as $line) {
            // استخراج و نگهداری فقط زمان از تاریخ
            $line = preg_replace_callback('/^\[(\d{2}-\w{3}-\d{4}\s)(\d{2}:\d{2}:\d{2})(\s[A-Za-z\/]+)\]\s/', function ($matches) {
                return $matches[2].' '; // فقط زمان باقی بماند
            }, $line);

            // حذف عبارت PHP Notice:
            $line = preg_replace('/PHP Notice:\s*/', '', $line);

            if (! empty(trim($line))) {
                $processedLines[] = $line;
            }
        }

        file_put_contents($logFilePath, implode("\n", $processedLines));
    }
}

require_once 'core.php';

class Route
{
    private static $routes = [];

    private static $default;

    public static function clearCache()
    {
        self::$keyboard = [];
    }

    public static function add($patterns, $action)
    {
        if (! is_array($patterns)) {
            $patterns = [$patterns];
        }

        foreach ($patterns as $pattern) {
            self::$routes[$pattern] = $action;
        }

        return new self();
    }

    public static function def($default)
    {
        self::$default = $default;

        return new self();
    }

    public static function handle($input)
    {
        foreach ($GLOBALS as $key => $value) {
            global $$key;
        }

        if (empty($input)) {
            return false;
        } else {
            error_log("input: $input");
            if (isset(self::$routes[$input])) {
                $action = self::$routes[$input];

                if (is_callable($action)) {
                    call_user_func($action);
                } elseif (is_string($action) && file_exists($action)) {
                    include_once $action;
                } else {
                }
            } else {
                // error_log("default route: self::$action");
                include_once self::$default;
            }
        }
    }
}
