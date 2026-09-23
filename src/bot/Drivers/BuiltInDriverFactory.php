<?php

namespace natilosir\bot\Bot\Drivers;

use Illuminate\Http\Client\Factory;
use InvalidArgumentException;
use natilosir\bot\Bot\Client\BaleClient;
use natilosir\bot\Bot\Contracts\BotDriver;
use natilosir\bot\Bot\Client\TelegramClient;

/**
 * Creates the package's built-in drivers.
 *
 * Keeping construction here gives DriverManager a single responsibility:
 * selecting and caching drivers, not knowing how each concrete driver is built.
 */
final class BuiltInDriverFactory {
    public function __construct( private readonly Factory $http ) {}

    public function make( string $name, array $config ): BotDriver {
        $name  = strtolower($name);
        $token = (string) ( $config['token'] ?? '' );

        if ( $token === '' ) {
            throw new InvalidArgumentException("Bot token for driver [{$name}] is not configured. Set bot.drivers.{$name}.token.");
        }

        return match ( $name ) {
            'telegram' => new TelegramClient($this->http, $token, (string) ( $config['base_url'] ?? 'https://api.telegram.org' ), $config),
            'bale'     => new BaleClient($this->http, $token, (string) ( $config['base_url'] ?? 'https://tapi.bale.ai' ), $config),
            default    => throw new InvalidArgumentException("Bot driver [{$name}] is not supported."),
        };
    }
}
