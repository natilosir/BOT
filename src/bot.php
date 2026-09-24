<?php

namespace natilosir\bot;

use natilosir\bot\Bot\Contracts\BotDriver;
use natilosir\bot\Bot\Drivers\Bale\BaleDriver;
use natilosir\bot\Bot\Drivers\Telegram\TelegramDriver;
use natilosir\bot\Bot\Manager\BotManager;
use natilosir\bot\Bot\Manager\PendingCall;

/**
 * Static bot facade.
 *
 * Common facade calls are lazy and return PendingCall. Use telegram()/bale()
 * when you explicitly want the concrete driver and immediate execution.
 *
 * @method static PendingCall api(string $method, array $data = [], string $httpMethod = 'POST')
 * @method static PendingCall request(string $method, array $data = [], string $httpMethod = 'POST')
 * @method static PendingCall sendMessage($chatID, $text, $reply_to_message_id = null, $reply_markup = null)
 * @method static PendingCall sendPhoto($chatID, $caption = null, $photo = null, $reply_to_message_id = null, $reply_markup = null)
 * @method static PendingCall sendDocument($chatID, $document = null, $caption = null, $reply_to_message_id = null, $reply_markup = null)
 * @method static PendingCall sendVideo($chatID, $video = null, $caption = null, $reply_to_message_id = null, $reply_markup = null)
 * @method static PendingCall sendAudio($chatID, $audio = null, $caption = null, $reply_to_message_id = null, $reply_markup = null)
 * @method static PendingCall sendVoice($chatID, $voice = null, $caption = null, $reply_to_message_id = null, $reply_markup = null)
 * @method static PendingCall forwardMessage($chatID, $from_chat_id, $message_id)
 * @method static PendingCall copyMessage($chatID, $from_chat_id, $message_id, $reply_markup = null)
 * @method static PendingCall deleteMessage($chatID, $message_id)
 * @method static PendingCall sendChatAction($chatID, $action)
 * @method static PendingCall answerCallbackQuery($callbackQueryIdOrData, $text = null, $showAlert = null, $url = null, $cacheTime = null)
 * @method static mixed row(array $buttons)
 * @method static array column($text, $callback_data = null, $url = null)
 * @method static bool supports(string $method)
 * @method static array file(string $path, ?string $name = null)
 *
 * @mixin BotManager
 * @mixin TelegramDriver
 * @mixin BaleDriver
 */
class bot extends Facade {
    protected static function getFacadeAccessor(): string {
        return BotManager::class;
    }

    public static function telegram(): TelegramDriver {
        return static::manager()->telegram();
    }

    public static function bale(): BaleDriver {
        return static::manager()->bale();
    }

    public static function driver( ?string $name = null ): BotDriver {
        return static::manager()->driver($name);
    }

    public static function currentDriver(): BotDriver {
        return static::manager()->currentDriver();
    }

    public static function useDriver( string $name ): BotManager {
        return static::manager()->useDriver($name);
    }

    public static function driverName(): string {
        return static::manager()->driverName();
    }

    private static function manager(): BotManager {
        /** @var BotManager $manager */
        $manager = static::resolveFacadeRoot();

        return $manager;
    }
}
