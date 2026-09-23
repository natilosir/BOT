<?php

namespace natilosir\bot\Bot\Manager;

use natilosir\bot\Bot\Contracts\BotDriver;
use natilosir\bot\Bot\Drivers\Bale\BaleDriver;
use natilosir\bot\Bot\Drivers\Telegram\TelegramDriver;

final class BotManager {
    public function __construct( private readonly DriverManager $drivers ) {}

    public function driver( ?string $name = null ): BotDriver {
        return $this->drivers->driver($name);
    }

    /**
     * Returns the concrete Telegram driver. PhpStorm can follow methods from
     * this return type into the exact trait where they are implemented.
     */
    public function telegram(): TelegramDriver {
        return $this->drivers->telegram();
    }

    /**
     * Returns the concrete Bale driver. PhpStorm can follow methods from this
     * return type into the exact Bale trait implementation.
     */
    public function bale(): BaleDriver {
        return $this->drivers->bale();
    }

    public function currentDriver(): BotDriver {
        return $this->drivers->driver();
    }

    public function useDriver( string $name ): static {
        $this->drivers->use($name);
        return $this;
    }

    public function driverName(): string {
        return $this->drivers->current();
    }

    public function supports( string $method ): bool {
        return method_exists($this->driver(), $method);
    }

    public function api( string $method, array $data = [], string $httpMethod = 'POST' ): PendingCall {
        return app(PendingCall::class, [
            'driver'     => $this->driver(),
            'method'     => $method,
            'data'       => $data,
            'httpMethod' => $httpMethod,
        ]);
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

    /** @deprecated Prefer driver('telegram'). */
    public function TelegramClient(): TelegramClient {
        $driver = $this->drivers->driver('telegram');

        if ( !$driver instanceof TelegramClient ) {
            throw new \RuntimeException('The telegram driver is not a TelegramClient instance.');
        }

        return $driver;
    }

    /** @deprecated Prefer driver('bale'). */
    public function BaleClient(): BaleClient {
        $driver = $this->drivers->driver('bale');

        if ( !$driver instanceof BaleClient ) {
            throw new \RuntimeException('The bale driver is not a BaleClient instance.');
        }

        return $driver;
    }

    public function __call( string $method, array $arguments ): mixed {
        $driver = $this->driver();

        if ( !method_exists($driver, $method) ) {
            $data       = isset($arguments[0]) && is_array($arguments[0]) ? $arguments[0] : [];
            $httpMethod = isset($arguments[1]) && is_string($arguments[1]) ? $arguments[1] : 'POST';
            return new PendingCall($driver, $method, $data, $httpMethod);
        }

        $driver->beginCapture();
        try {
            $returnValue = $driver->{$method}(...$arguments);
        } finally {
            $captured = $driver->endCapture();
        }

        if ( $captured === null ) {
            return $returnValue;
        }

        return app(PendingCall::class, [
            'driver'     => $driver,
            'method'     => $captured['method'],
            'data'       => $captured['data'],
            'httpMethod' => $captured['httpMethod'],
        ]);
    }
}