<?php

namespace natilosir\bot\bot;

use natilosir\bot\bot\Traits\ApiTrait;
use natilosir\bot\bot\Traits\BotProfileTrait;
use natilosir\bot\bot\Traits\BusinessTrait;
use natilosir\bot\bot\Traits\CallbackTrait;
use natilosir\bot\bot\Traits\ChatManagementTrait;
use natilosir\bot\bot\Traits\CoreTrait;
use natilosir\bot\bot\Traits\DirectMessageTrait;
use natilosir\bot\bot\Traits\EditMessageTrait;
use natilosir\bot\bot\Traits\EphemeralTrait;
use natilosir\bot\bot\Traits\ForumTrait;
use natilosir\bot\bot\Traits\GameTrait;
use natilosir\bot\bot\Traits\GiftTrait;
use natilosir\bot\bot\Traits\GuestTrait;
use natilosir\bot\bot\Traits\InlineTrait;
use natilosir\bot\bot\Traits\JoinRequestTrait;
use natilosir\bot\bot\Traits\KeyboardTrait;
use natilosir\bot\bot\Traits\LocationTrait;
use natilosir\bot\bot\Traits\ManagedBotTrait;
use natilosir\bot\bot\Traits\MediaTrait;
use natilosir\bot\bot\Traits\MessageTrait;
use natilosir\bot\bot\Traits\PaymentTrait;
use natilosir\bot\bot\Traits\PollTrait;
use natilosir\bot\bot\Traits\RichMessageTrait;
use natilosir\bot\bot\Traits\StickerTrait;
use natilosir\bot\bot\Traits\VerificationTrait;

class BotManager {
    use ApiTrait, CoreTrait, KeyboardTrait, MessageTrait, MediaTrait, LocationTrait, PollTrait, CallbackTrait, EditMessageTrait, ChatManagementTrait, ForumTrait, BotProfileTrait, InlineTrait, StickerTrait, PaymentTrait, GameTrait, BusinessTrait, GiftTrait, VerificationTrait, ManagedBotTrait, RichMessageTrait, EphemeralTrait, DirectMessageTrait, GuestTrait, JoinRequestTrait;

    public function __construct( protected TelegramClient $client ) {}

    public function client(): TelegramClient {
        return $this->client;
    }
}
