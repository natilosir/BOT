<?php

namespace natilosir\bot;

class Route {
    public function __construct( Request $request ) {
        self::$request = $request;
    }

    private static      $routes               = [];
    private static      $regexRoutes          = [];
    private static      $default              = null;
    private static      $autoloaderRegistered = false;
    private static      $instance             = null;
    private static      $request              = null;
    private static      $configclear          = true;
    private static      $states               = [];
    private static bool $dispatchRegistered   = false;
    private static bool $dispatched           = false;

    public static function init(): void {
        self::dispatch();
    }

    public static function dispatch(): void {
        if ( self::$dispatched ) {
            return;
        }
        self::$dispatched = true;
        lg(self::processRequest());
    }

    private static function registerAutoDispatch(): void {
        if ( self::$dispatchRegistered ) {
            return;
        }
        self::$dispatchRegistered = true;
        register_shutdown_function(static function (): void {
            $lastError  = error_get_last();
            $fatalTypes = [ E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR ];
            if ( $lastError === null || !in_array($lastError['type'], $fatalTypes, true) ) {
                self::dispatch();
            }
        });
    }

    public static function processRequest() {
        $input = self::normalizeInput(self::$request->text ?? '');
        lg("Request Input: " . $input);

        if ( !empty($input) && isset(self::$routes[$input]) ) {
            if ( isset(self::$states[$input]) ) {
                State::set(self::$states[$input]);
                self::$configclear = false;
            }
            return self::runAction(self::$routes[$input], self::$request);
        }

        foreach ( self::$regexRoutes as $pattern => $action ) {
            if ( preg_match($pattern, $input) ) {
                lg("Regex matched: {$pattern} ");
                return self::runAction($action, self::$request);
            }
        }

        require_once paths()->route('state.php');

        $stateHandled = State::init(self::$request);

        if ( $stateHandled ) {
            return;
        }

        if ( self::$default ) {
            return self::runAction(self::$default, self::$request);
        }

        throw new RuntimeException("Route not found for input: " . $input);
    }

    public static function state( $stateName ) {
        $lastRoute = array_key_last(self::$routes);
        if ( $lastRoute ) {
            self::$states[$lastRoute] = $stateName;
        }
        return new self(self::$request);
    }

    public static function add( $uri, $action ) {
        self::registerAutoDispatch();
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

    public static function regex( $pattern, $action ) {
        self::registerAutoDispatch();
        if ( is_array($pattern) ) {
            foreach ( $pattern as $p ) {
                self::$regexRoutes[$p] = $action;
            }
        }
        else {
            self::$regexRoutes[$pattern] = $action;
        }
        return new self(self::$request ?? new Request());
    }

    public static function def( $default ) {
        self::registerAutoDispatch();
        self::$default = $default;
        return new self(self::$request);
    }

    public static function registerRoute( $uri, $action ) {
        self::$routes[self::normalizeInput($uri)] = $action;
    }

    public static function response( $data = [], $status = 200 ) {
        http_response_code($status);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode([
            'status' => $status,
            'data'   => $data,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function registerState( $uri, $stateName ) {
        self::$states[self::normalizeInput($uri)] = $stateName;
    }

    private static function normalizeInput( $input ) {
        $normalized = is_string($input) ? trim($input) : '';

        $normalized = str_replace([ 'ي', 'ك', "\xE2\x80\x8C", "\xE2\x80\x8D" ], [ 'ی', 'ک', ' ', ' ' ], $normalized);

        $normalized = preg_replace('/[\s\x{00A0}\x{200B}]+/u', ' ', $normalized);

        return trim($normalized);
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
            throw new RuntimeException("Controller class not found: {$controller}");
        }

        $instance = new $controller();

        if ( !method_exists($instance, $method) ) {
            if ( method_exists($instance, '__invoke') ) {
                return $instance->__invoke($request);
            }
            throw new RuntimeException("Method not found: {$controller}::{$method}");
        }
        if ( self::$configclear ) {
            State::clear();
        }

        return $instance->$method($request);
    }
}