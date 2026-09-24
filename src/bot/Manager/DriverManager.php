<?php

namespace natilosir\bot\Bot\Manager;

use Closure;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use LogicException;
use natilosir\bot\Bot\Contracts\BotDriver;
use natilosir\bot\Bot\Drivers\Bale\BaleDriver;
use natilosir\bot\Bot\Drivers\BuiltInDriverFactory;
use natilosir\bot\Bot\Drivers\Telegram\TelegramDriver;
use natilosir\bot\Bot\Webhook\WebhookDriverResolver;
use natilosir\bot\Bot\Webhook\WebhookRequest;

final class DriverManager {
    public function __construct( private readonly Factory $http, private readonly array $config, private readonly ?WebhookDriverResolver $webhookResolver = null ) {
        $this->builtInFactory = new BuiltInDriverFactory($this->http);
    }

    private array                         $drivers         = [];
    private array                         $extensions      = [];
    private string                        $current         = '';
    private bool                          $webhookResolved = false;
    private readonly BuiltInDriverFactory $builtInFactory;

    public function defaultDriver(): string {
        return $this->normalizeName((string) ( Arr::get($this->config, 'default') ?? Arr::get($this->config, 'driver') ?? 'telegram' ));
    }

    public function current(): string {
        $this->resolveWebhookContext();
        return $this->current;
    }

    public function use( string $name ): static {
        $name = $this->normalizeName($name);

        $this->driver($name, detectWebhook : false);
        $this->current         = $name;
        $this->webhookResolved = true;

        return $this;
    }

    public function driver( ?string $name = null, bool $detectWebhook = true ): BotDriver {
        if ( $name === null ) {
            $name = $detectWebhook ? $this->current() : ( $this->current !== '' ? $this->current : $this->defaultDriver() );
        }

        $name = $this->normalizeName($name);

        return $this->drivers[$name] ??= $this->createDriver($name);
    }

    public function telegram(): TelegramDriver {
        $driver = $this->driver('telegram');

        if ( !$driver instanceof TelegramDriver ) {
            throw new LogicException('The [telegram] driver was extended with an incompatible implementation.');
        }

        return $driver;
    }

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
        $name = $this->normalizeName($name);

        $this->extensions[$name] = $factory;
        unset($this->drivers[$name]);

        return $this;
    }

    public function forget( string $name ): static {
        unset($this->drivers[$this->normalizeName($name)]);
        return $this;
    }

    public function config( ?string $name = null ): array {
        return $name === null ? $this->config : $this->driverConfig($name);
    }

    public function configuredDriverNames(): array {
        $drivers = Arr::get($this->config, 'drivers', []);

        return is_array($drivers) ? array_map(fn( string $name ): string => $this->normalizeName($name), array_keys($drivers)) : [];
    }

    private function createDriver( string $name ): BotDriver {
        $config = $this->driverConfig($name);

        if ( isset($this->extensions[$name]) ) {
            $driver = ( $this->extensions[$name] )($this->http, $config, $this);

            if ( !$driver instanceof BotDriver ) {
                throw new InvalidArgumentException("Custom bot driver [{$name}] must implement " . BotDriver::class,);
            }

            return $driver;
        }

        return $this->builtInFactory->make($name, $config);
    }

    private function resolveWebhookContext(): void {
        if ( $this->webhookResolved ) {
            return;
        }

        if ( ( $detected = $this->detectWebhook() ) !== null ) {
            $this->current = $this->normalizeName($detected);
        }
        elseif ( $this->current === '' ) {
            $this->current = $this->defaultDriver();
        }

        $this->webhookResolved = true;
    }

    private function driverConfig( string $name ): array {
        $config = Arr::get($this->config, 'drivers.' . $this->normalizeName($name), []);

        return is_array($config) ? $config : [];
    }

    private function normalizeName( string $name ): string {
        return strtolower(trim($name));
    }
}