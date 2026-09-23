<?php

namespace natilosir\bot\Bot\Drivers\Bale;

use natilosir\bot\Bot\Drivers\Bale\Traits\ApiTrait;
use natilosir\bot\Bot\Drivers\Bale\Traits\BusinessTrait;
use natilosir\bot\Bot\Drivers\Bale\Traits\ChatTrait;
use natilosir\bot\Bot\Drivers\Bale\Traits\CoreTrait;
use natilosir\bot\Bot\Drivers\Bale\Traits\EditMessageTrait;
use natilosir\bot\Bot\Drivers\Bale\Traits\KeyboardTrait;
use natilosir\bot\Bot\Drivers\Bale\Traits\MediaTrait;
use natilosir\bot\Bot\Drivers\Bale\Traits\MessageTrait;
use natilosir\bot\Bot\Drivers\Bale\Traits\PaymentTrait;
use natilosir\bot\Bot\Drivers\Bale\Traits\StickerTrait;

/**
 * Bale Bot API driver.
 *
 * Only Bale-specific capabilities are composed here. Telegram concerns are not
 * loaded into this class, so unsupported Telegram methods cannot leak into Bale.
 */
class BaleDriver extends \natilosir\bot\Bot\Drivers\AbstractBotDriver {
    use ApiTrait;
    use CoreTrait;
    use MessageTrait;
    use MediaTrait;
    use KeyboardTrait;
    use ChatTrait;
    use EditMessageTrait;
    use StickerTrait;
    use PaymentTrait;
    use BusinessTrait;

    protected array $supportedMethods = BaleMethodCatalog::ALL;

    public function fileUrl( string $filePath ): string {
        return $this->apiBaseUrl . '/file/bot' . $this->botToken . '/' . ltrim($filePath, '/');
    }

    public function name(): string {
        return 'bale';
    }
}
