<?php

namespace natilosir\bot\Bot\Client;

use Illuminate\Http\Client\Factory;
use natilosir\bot\Bot\Drivers\Bale\BaleDriver;

class BaleClient extends BaleDriver {
    public function __construct( Factory $http, ?string $token = null, ?string $baseUrl = null, ?array $config = null ) {
        $config  ??= (array) paths()->config('bot.drivers.bale');
        $token   ??= (string) ( $config['token'] );
        $baseUrl ??= (string) ( $config['base_url'] );

        parent::__construct($http, $token, $baseUrl, $config);
    }
}
