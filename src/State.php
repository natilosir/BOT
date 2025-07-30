<?php

namespace natilosir\bot;

use app\Models\User;

class State {
    private static $states               = [];
    private static $default              = null;
    private static $autoloaderRegistered = false;
    private static $instance             = null;
    private static $request              = null;

    private function __construct( Request $request ) {
        self::$request = $request;
    }

    public static function init(): void {
        if ( self::$instance === null ) {
            $request = new Request();
            if ( $request === null ) {
                throw new \RuntimeException('Request initialization failed');
            }
            self::$instance = new self($request);
            self::processRequest();
        }
    }

    private static function processRequest(): void {
        if ( self::$request === null ) {
            lg("Request object is null");
            return;
        }

        $userState = self::getUserState();

        if ( $userState && isset(self::$states[$userState]) ) {
            self::runAction(self::$states[$userState], self::$request);
            exit();
        }

        if ( $userState === null ) {
            return;
        }

        $input = self::normalizeInput(self::$request->getInput() ?? '');
        if ( empty($input) ) {
            return;
        }

        if ( isset(self::$states[$input]) ) {
            self::runAction(self::$states[$input], self::$request);
            exit();
        }

        if ( self::$default ) {
            self::runAction(self::$default, self::$request);
            exit();
        }
    }

    private static function getUserState() {
        try {
            $userId = self::$request->fromID;
            $user   = User::where('user_id', $userId)->whereNotNull('state')->first();

            return $user ? $user->state : null;
        } catch ( \Exception $e ) {
            lg("Error getting user state: " . $e->getMessage());
            return null;
        }
    }

    public static function set( string $state ) {
        try {
            $userId = ( new Request() )->fromID;
            User::Update([ 'user_id' => $userId ], [ 'state' => $state ]);
        } catch ( \Exception $e ) {
            lg("Error setting user state: " . $e->getMessage());
        }
    }

    public static function clear() {
        try {
            self::set('');
        } catch ( \Exception $e ) {
            lg("Error clearing user state: " . $e->getMessage());
        }
    }

    public static function add( $state, $action ) {
        if ( is_array($state) ) {
            foreach ( $state as $s ) {
                self::$states[self::normalizeInput($s)] = $action;
            }
        }
        else {
            self::$states[self::normalizeInput($state)] = $action;
        }

        return __CLASS__;
    }

    public static function def( $default ) {
        self::$default = $default;
        return __CLASS__;
    }

    private static function normalizeInput( $input ) {
        $normalized = trim($input);
        $normalized = str_replace([ 'ي', 'ك' ], [ 'ی', 'ک' ], $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        return $normalized;
    }

    private static function runAction( $action, Request $request ) {
        try {
            $input = $request->getInput() ?? '';
            if ( is_array($action) ) {
                return self::callController($action[0], $action[1], $request);
            }
            elseif ( is_callable($action) ) {
                lg("Calling Callable Function");
                return call_user_func($action, $request);
            }
            elseif ( is_string($action) ) {
                lg("Calling Controller: {$action}::__invoke");
                return self::callController($action, '__invoke', $request);
            }
        } catch ( \Exception $e ) {
            lg("Error in runAction: " . $e->getMessage());
            echo "An error occurred. Please try again later.";
            die();
        }
    }

    private static function callController( $controller, $method, Request $request ) {
        lg("Calling Controller: {$controller}::{$method}");
        try {
            if ( !class_exists($controller) && !str_contains($controller, '\\') ) {
                $controller = "Controllers\\" . ucfirst($controller);
            }

            if ( !class_exists($controller) ) {
                throw new \Exception("Controller class not found: {$controller}");
            }

            $instance = new $controller();

            if ( !method_exists($instance, $method) ) {
                if ( method_exists($instance, '__invoke') ) {
                    lg("Method {$method} not found, calling __invoke instead");
                    $instance->__invoke($request);
                    die();
                }
                throw new \Exception("Method not found: {$controller}::{$method}");
            }
            $instance->$method($request);
            die();
        } catch ( \Exception $e ) {
            echo "Error: " . $e->getMessage();
            die();
        }
    }
}