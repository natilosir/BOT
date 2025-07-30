<?php

namespace natilosir\bot;

class Route {
    private static $routes               = [];
    private static $default              = null;
    private static $autoloaderRegistered = false;
    private static $instance             = null;
    private static $request              = null;
    private static $configclear          = true;
    private static $states               = [];

    public function __construct( Request $request ) {
        self::$request = $request ?? new Request();
    }

    public static function init(): void {
        if ( self::$instance === null ) {
            self::$instance = new self(new Request());
            self::processRequest();
        }
    }

    public static function processRequest(): void {
        self::$request = new Request();
        $input         = self::normalizeInput(self::$request->text);
        lg("Request Input: " . $input);

        //         1. سپس مسیرهای تعریف شده را بررسی می‌کنیم
        if ( !empty($input) && isset(self::$routes[$input]) ) {
            // اگر مسیر state داشته باشد
            if ( isset(self::$states[$input]) ) {
                State::set(self::$states[$input]);
                self::$configclear = false;
            }
            self::runAction(self::$routes[$input], self::$request);
            return;
        }

        // 2. ابتدا State را بررسی می‌کنیم
        require_once __DIR__ . '/../../../../Router/state.php';
        State::init();

        // 3. در نهایت متد پیش‌فرض
        if ( self::$default ) {
            self::runAction(self::$default, self::$request);
            return;
        }

        throw new \Exception("Route not found for input: " . $input);
    }

    public static function state( $stateName ) {
        $lastRoute = array_key_last(self::$routes);
        if ( $lastRoute ) {
            self::$states[$lastRoute] = $stateName;
        }
        return new self(self::$request ?? new Request());
    }

    public static function add( $uri, $action ) {
        if ( is_array($uri) ) {
            foreach ( $uri as $u ) {
                self::registerRoute($u, $action);
            }
        }
        else {
            self::registerRoute($uri, $action);
        }
        return new self(self::$request ?? new Request());
    }

    public static function def( $default ) {
        self::$default = $default;
        return new self(self::$request ?? new Request());
    }

    public static function registerRoute( $uri, $action ) {
        self::$routes[self::normalizeInput($uri)] = $action;
    }

    public static function registerState( $uri, $stateName ) {
        self::$states[self::normalizeInput($uri)] = $stateName;
    }

    private static function normalizeInput( $input ) {
        $normalized = trim($input);
        $normalized = str_replace([ 'ي', 'ك' ], [ 'ی', 'ک' ], $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        return $normalized;
    }

    private static function runAction( $action, Request $request ) {
        if ( is_array($action) ) {
            return self::callController($action[0], $action[1], $request);
        }
        elseif ( is_callable($action) ) {
            return call_user_func($action, $request);
        }
        elseif ( is_string($action) ) {
            return self::callController($action, '__invoke', $request);
        }
    }

    private static function callController( $controller, $method, Request $request ) {
        lg("Calling Controller: {$controller}::{$method}");
        if ( !class_exists($controller) && !str_contains($controller, '\\') ) {
            $controller = "Controllers\\" . ucfirst($controller);
        }

        if ( !class_exists($controller) ) {
            throw new \Exception("Controller class not found: {$controller}");
        }

        $instance = new $controller();

        if ( !method_exists($instance, $method) ) {
            if ( method_exists($instance, '__invoke') ) {
                return $instance->__invoke($request);
            }
            throw new \Exception("Method not found: {$controller}::{$method}");
        }
        if ( self::$configclear ) {
            State::clear();
        }

        return $instance->$method($request);
    }
}