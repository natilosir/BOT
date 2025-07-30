<?php
namespace natilosir\bot;

class RouteDefinition {
    private $uris;
    private $action;

    public function __construct($uris, $action) {
        $this->uris = is_array($uris) ? $uris : [$uris];
        $this->action = $action;

        foreach ($this->uris as $uri) {
            Route::registerRoute($uri, $action);
        }
    }

    public function state($stateName) {
        foreach ($this->uris as $uri) {
            Route::registerState($uri, $stateName);
        }
        return $this;
    }
}
