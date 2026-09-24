<?php

namespace natilosir\bot\Bot\Manager;

use Closure;
use natilosir\bot\Bot\Client\BaleClient;
use natilosir\bot\Bot\Client\TelegramClient;
use natilosir\bot\Bot\Contracts\BotDriver;
use natilosir\bot\Bot\Drivers\Bale\BaleDriver;
use natilosir\bot\Bot\Drivers\Telegram\TelegramDriver;
use RuntimeException;

final class BotManager {
    public function __construct( private readonly DriverManager $drivers ) {}

    public function driver( ?string $name = null ): BotDriver {
        return $this->drivers->driver($name);
    }

    public function telegram(): TelegramDriver {
        return $this->drivers->telegram();
    }

    public function bale(): BaleDriver {
        return $this->drivers->bale();
    }

    public function currentDriver(): BotDriver {
        return $this->driver();
    }

    public function useDriver( string $name ): static {
        $this->drivers->use($name);
        return $this;
    }

    public function driverName(): string {
        return $this->drivers->current();
    }

    public function supports( string $method ): bool {
        $driver = $this->driver();

        return method_exists($driver, $method) || $driver->supports($method);
    }

    public function api( string $method, array $data = [], string $httpMethod = 'POST' ): PendingCall {
        return $this->pendingCall($this->driver(), $method, $data, $httpMethod);
    }

    public function request( string $method, array $data = [], string $httpMethod = 'POST' ): PendingCall {
        return $this->api($method, $data, $httpMethod);
    }

    public function file( string $path, ?string $name = null ): array {
        return [
            'tmp_name' => $path,
            'name'     => $name ? : basename($path),
        ];
    }

    /** @deprecated Prefer telegram() or driver('telegram'). */
    public function TelegramClient(): TelegramClient {
        $driver = $this->drivers->driver('telegram');

        if ( !$driver instanceof TelegramClient ) {
            throw new RuntimeException('The telegram driver is not a TelegramClient instance.');
        }

        return $driver;
    }

    /** @deprecated Prefer bale() or driver('bale'). */
    public function BaleClient(): BaleClient {
        $driver = $this->drivers->driver('bale');

        if ( !$driver instanceof BaleClient ) {
            throw new RuntimeException('The bale driver is not a BaleClient instance.');
        }

        return $driver;
    }

    /**
     * Route facade calls through the active driver without executing Bot API
     * traffic immediately. Driver helper methods are allowed to build their
     * payload normally; AbstractBotDriver captures the resulting API call and
     * this manager turns it into a lazy PendingCall.
     */
    public function __call( string $method, array $arguments ): mixed {
        $driver = $this->driver();

        if ( !method_exists($driver, $method) ) {
            return $this->pendingCall(
                $driver,
                $method,
                isset($arguments[0]) && is_array($arguments[0]) ? $arguments[0] : [],
                isset($arguments[1]) && is_string($arguments[1]) ? $arguments[1] : 'POST',
            );
        }

        $driver->beginCapture();

        try {
            $returnValue = $driver->{$method}(...$arguments);
        } finally {
            $capturedCall = $driver->endCapture();
        }

        if ( $capturedCall === null ) {
            return $returnValue;
        }

        return $this->pendingCall(
            $driver,
            $capturedCall['method'],
            $capturedCall['data'],
            $capturedCall['httpMethod'],
            $capturedCall['executor'] ?? null,
            $capturedCall['url'] ?? null,
        );
    }

    private function pendingCall( BotDriver $driver, string $method, array $data = [], string $httpMethod = 'POST', ?Closure $executor = null, ?string $url = null ): PendingCall {
        return new PendingCall($driver, $method, $data, strtoupper($httpMethod), $executor, $url,);
    }
}
