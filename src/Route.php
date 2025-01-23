<?php

namespace natilosir\bot;

function rc($input)
{
    return str_replace('.', '/', $input);
}
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

        $action  = self::$routes[$input];
        $default = self::$default;

        if (empty($action)) {
            lg("input: $input");

            return include_once 'controller/'.rc($default).'.php';
        } elseif (is_string($action)) {
            if (file_exists('controller/'.rc($action).'.php')) {
                lg("Route: '$input' => 'controller/".rc($action).".php'");

                return include_once 'controller/'.rc($action).'.php';
            } else {
                lg("The controller file was not found => 'controller/".rc($action).".php'  404 not found");
            }
        } elseif (is_callable($action)) {
            lg("func: $input");

            return call_user_func($action);
        }
    }
}
