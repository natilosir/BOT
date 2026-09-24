<?php

namespace natilosir\bot\Bot\Webhook;

use natilosir\bot\Bot\Contracts\WebhookAwareDriver;
use natilosir\bot\Bot\Manager\DriverManager;
use RuntimeException;

final class WebhookDriverResolver {
    public function resolve( DriverManager $manager, ?WebhookRequest $request = null ): ?string {
        $request ??= WebhookRequest::capture();
        //        if ( !$request->isPost() ) {
        //            return null;
        //        }
        $matches = [];

        foreach ( $manager->configuredDriverNames() as $name ) {
            try {
                $driver = $manager->driver($name, detectWebhook : false);
            } catch ( \Throwable $exception ) {
                dd($exception);
            }

            if ( $driver instanceof WebhookAwareDriver && $driver->matchesWebhook($request) ) {
                $matches[] = $name;
            }
        }

        if ( count($matches) > 1 ) {
            throw new RuntimeException('Webhook driver detection is ambiguous. Configure a unique webhook path/secret for each driver.');
        }

        return $matches[0] ?? null;
    }
}
