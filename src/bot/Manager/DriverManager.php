<?php

namespace natilosir\bot\Bot\Manager;

use Closure;
use Illuminate\Http\Client\Factory;
use InvalidArgumentException;
use LogicException;
use natilosir\bot\Bot\Contracts\BotDriver;
use natilosir\bot\Bot\Drivers\Bale\BaleDriver;
use natilosir\bot\Bot\Drivers\BuiltInDriverFactory;
use natilosir\bot\Bot\Drivers\Telegram\TelegramDriver;
use natilosir\bot\Bot\Webhook\WebhookDriverResolver;
use natilosir\bot\Bot\Webhook\WebhookRequest;

final class DriverManager {
    private array  $drivers         = [];
    private array  $extensions      = [];
    private string $current;
    private bool   $webhookResolved = false;

    public function __construct( private readonly Factory $http, private ?array $config = null, private ?WebhookDriverResolver $webhookResolver = null, private ?BuiltInDriverFactory $builtInFactory = null ) {
        $this->config          ??= (array) paths()->config('bot', []);
        $this->webhookResolver ??= new WebhookDriverResolver();
        $this->builtInFactory  ??= new BuiltInDriverFactory($this->http);
        $this->current         = $this->defaultDriver();
    }

    public function defaultDriver(): string {
        return strtolower((string) ( $this->config['default'] ?? $this->config['driver'] ?? 'telegram' ));
    }

    public function current(): string {
        $this->resolveWebhookContext();
        return $this->current;
    }

    public function use( string $name ): static {
        $name = strtolower($name);
        $this->driver($name, detectWebhook : false);
        $this->current         = $name;
        $this->webhookResolved = true;

        return $this;
    }

    public function driver( ?string $name = null, bool $detectWebhook = true ): BotDriver {
        if ( $name === null ) {
            $name = $detectWebhook ? $this->current() : $this->current;
        }

        $name = strtolower($name);

        if ( isset($this->drivers[$name]) ) {
            return $this->drivers[$name];
        }

        $config = $this->driverConfig($name);

        if ( isset($this->extensions[$name]) ) {
            $driver = ( $this->extensions[$name] )($this->http, $config, $this);
            if ( !$driver instanceof BotDriver ) {
                throw new InvalidArgumentException("Custom bot driver [{$name}] must implement " . BotDriver::class);
            }

            return $this->drivers[$name] = $driver;
        }

        return $this->drivers[$name] = $this->builtInFactory->make($name, $config);
    }

    /**
     * Strongly typed Telegram accessor for IDE/static analysis.
     */
    public function telegram(): TelegramDriver {
        $driver = $this->driver('telegram');

        if ( !$driver instanceof TelegramDriver ) {
            throw new LogicException('The [telegram] driver was extended with an incompatible implementation.');
        }

        return $driver;
    }

    /**
     * Strongly typed Bale accessor for IDE/static analysis.
     */
    public function bale(): BaleDriver {
        $driver = $this->driver('bale');

        if ( !$driver instanceof BaleDriver ) {
            throw new LogicException('The [bale] driver was extended with an incompatible implementation.');
        }

        return $driver;
    }

    public function detectWebhook( ?WebhookRequest $request = null ): ?string {
        return $this->webhookResolver?->resolve($this, $request);
    }

    public function extend( string $name, Closure $factory ): static {
        $name                    = strtolower($name);
        $this->extensions[$name] = $factory;
        unset($this->drivers[$name]);

        return $this;
    }

    public function forget( string $name ): static {
        unset($this->drivers[strtolower($name)]);
        return $this;
    }

    public function config( ?string $name = null ): array {
        return $name === null ? $this->config : $this->driverConfig($name);
    }

    public function configuredDriverNames(): array {
        $drivers = $this->config['drivers'] ?? [];
        return is_array($drivers) ? array_map('strtolower', array_keys($drivers)) : [];
    }

    private function resolveWebhookContext(): void {
        if ( $this->webhookResolved ) {
            return;
        }

        $detected = $this->detectWebhook();

        if ( $detected !== null ) {
            $this->current = $detected;
        }

        $this->webhookResolved = true;
    }

    private function driverConfig( string $name ): array {
        $drivers = $this->config['drivers'] ?? [];
        $config  = is_array($drivers) ? ( $drivers[$name] ?? [] ) : [];

        return is_array($config) ? $config : [];
    }
}
