<?php

namespace natilosir\bot\Bot\Client;

use Illuminate\Http\Client\Factory;
use natilosir\bot\Bot\Drivers\Telegram\TelegramDriver;

class TelegramClient extends TelegramDriver {
    public function __construct( Factory $http, ?string $token = null, ?string $baseUrl = null, ?array $config = null ) {
        $config  ??= (array) paths()->config('bot.drivers.telegram', []);
        $token   ??= (string) ($config['token'] ?? '');
        $baseUrl ??= (string) ($config['base_url'] ?? 'https://api.telegram.org');

        parent::__construct($http, $token, $baseUrl, $config);
    }
}
