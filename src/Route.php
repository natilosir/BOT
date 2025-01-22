<?php
namespace natilosir\bot;

class Route
{
    private static $routes = [];

    private static $default;

    public static function clearCache()
    {
        self::$routes = [];
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
        }

        $action = self::$routes[$input];
        $default = self::$default;

        if (empty($action)) {
            lg("input: $input");
            return include_once "controller/".$default.".php";
        }

        elseif (is_string($action) && file_exists("controller/".$action.".php")) {
            lg("Route: '$input' => 'controller/".$action.".php'");
            return include_once "controller/".$action.".php";
        } elseif (is_callable($action)) {
            lg("func: $input");
            return call_user_func($action);
        }
    }
}
