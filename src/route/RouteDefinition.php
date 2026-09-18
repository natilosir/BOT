<?php

namespace natilosir\bot\route;

class RouteDefinition {
    public function __construct( $uris, $action ) {
        $this->uris   = is_array($uris) ? $uris : [ $uris ];
        $this->action = $action;

        foreach ( $this->uris as $uri ) {
            Route::registerRoute($uri, $action);
        }
    }

    private $uris;
    private $action;

    public function state( $stateName ) {
        foreach ( $this->uris as $uri ) {
            Route::registerState($uri, $stateName);
        }
        return $this;
    }
}
