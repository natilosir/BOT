<?php

namespace natilosir\bot\Bot\Contracts;

use natilosir\bot\Bot\Webhook\WebhookRequest;

interface WebhookAwareDriver {
    public function matchesWebhook( WebhookRequest $request ): bool;
}
