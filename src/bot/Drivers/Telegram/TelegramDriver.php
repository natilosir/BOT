<?php

namespace natilosir\bot\Bot\Drivers\Telegram;

use natilosir\bot\Bot\Drivers\Telegram\Traits\ApiTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\BotProfileTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\BusinessTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\CallbackTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\ChatManagementTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\CoreTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\DirectMessageTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\EditMessageTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\EphemeralTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\ForumTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\GameTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\GiftTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\GuestTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\InlineTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\JoinRequestTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\KeyboardTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\LocationTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\ManagedBotTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\MediaTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\MessageTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\PaymentTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\PollTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\RichMessageTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\StickerTrait;
use natilosir\bot\Bot\Drivers\Telegram\Traits\VerificationTrait;
use natilosir\bot\Bot\Webhook\WebhookRequest;

class TelegramDriver extends \natilosir\bot\Bot\Drivers\AbstractBotDriver {
    protected array $supportedMethods = TelegramMethodCatalog::ALL;

    use ApiTrait, BotProfileTrait, BusinessTrait, CallbackTrait,
        ChatManagementTrait, CoreTrait, DirectMessageTrait, EditMessageTrait,
        EphemeralTrait, ForumTrait, GameTrait, GiftTrait, GuestTrait, InlineTrait,
        JoinRequestTrait, KeyboardTrait, LocationTrait, ManagedBotTrait, MediaTrait,
        MessageTrait, PaymentTrait, PollTrait, RichMessageTrait, StickerTrait, VerificationTrait;

    public function fileUrl( string $filePath ): string {
        return $this->apiBaseUrl . '/file/bot' . $this->botToken . '/' . ltrim($filePath, '/');
    }

    public function matchesWebhook( WebhookRequest $request ): bool {
        if ( !parent::matchesWebhook($request) ) {
            return false;
        }

        $secret = (string) $this->config('webhook.secret_token', '');
        if ( $secret === '' ) {
            return true;
        }

        $received = $request->header('x-telegram-bot-api-secret-token');
        return $received !== null && hash_equals($secret, $received);
    }

    public function name(): string {
        return 'telegram';
    }
}
