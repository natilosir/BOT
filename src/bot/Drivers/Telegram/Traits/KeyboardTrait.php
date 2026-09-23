<?php

namespace natilosir\bot\Bot\Drivers\Telegram\Traits;

trait KeyboardTrait
{
    /** @var array<int, array<int, array<string, mixed>>> */
    private static array $keyboard = [];

    public function clearCache()
    {
        static::$keyboard = [];
        return $this;
    }

    public function row($buttons)
    {
        static::$keyboard[] = $buttons;
        return $this;
    }

    public function column($text, $callback_data = null, $url = null)
    {
        $button = ['text' => $text];

        if ($callback_data !== null) {
            $button['callback_data'] = $callback_data;
        }

        if ($url !== null) {
            $button['url'] = $url;
        }

        return $button;
    }

    public function inline($chatID, $second_OR_text, $message_id, $copy = false)
    {
        $keyboard = static::$keyboard;
        static::$keyboard = []; // ریست بعد از استفاده

        $reply_markup = json_encode([
            'inline_keyboard' => $keyboard
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($copy === 'edit') {
            return $this->editMessageReplyMarkup($chatID, $message_id, $reply_markup);
        }

        if ($copy) {
            return $this->copyMessage($chatID, $second_OR_text, $message_id, $reply_markup);
        }

        return $this->sendMessage($chatID, $second_OR_text, $message_id, $reply_markup);
    }

    public function keyboard($chatID, $second_OR_text, $message_id, $copy = false, $resize = true, $one_time = false)
    {
        $keyboard = static::$keyboard;
        static::$keyboard = [];

        $reply_markup = collect([
            'keyboard' => $keyboard,
            'resize_keyboard' => $resize,
            'one_time_keyboard' => $one_time,
        ])->toJson();
        
        if ($copy) {
            return $this->copyMessage($chatID, $second_OR_text, $message_id, $reply_markup);
        }

        return $this->sendMessage($chatID, $second_OR_text, $message_id, $reply_markup);
    }

    public function forceReply(array $options = []): string
    {
        return json_encode(array_merge(['force_reply' => true], $options));
    }

    public function removeKeyboard(array $options = []): string
    {
        return json_encode(array_merge(['remove_keyboard' => true], $options));
    }
}