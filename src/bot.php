<?php

namespace natilosir\bot;

require_once 'http.php';

class bot
{
    private static $keyboard = [];

    public static function clearCache()
    {
        self::$keyboard = [];
    }

    public static function row($buttons)
    {
        self::$keyboard[] = $buttons;

        return new self();
    }

    public static function column($text, $callback_data = null, $url = null)
    {
        $button = ['text' => $text];
        if ($callback_data) {
            $button['callback_data'] = $callback_data;
        }
        if ($url) {
            $button['url'] = $url;
        }

        return $button;
    }

    public static function sendChatAction($chatID, $action)
    {
        $data = [
            'chat_id' => $chatID,
            'action'  => $action,
        ];

        return http('sendChatAction', $data);
    }

    public static function alert($query_id, $text, $show_alert = false)
    {
        $data = [
            'callback_query_id' => $query_id,
            'text'              => $text,
            'show_alert'        => $show_alert ? 'true' : 'false',
        ];

        return http('answerCallbackQuery', $data);
    }

    public static function sendMessage($chatID, $text, $reply_to_message_id = null, $reply_markup = null)
    {
        $data = [
            'chat_id'    => $chatID,
            'text'       => $text,
            'parse_mode' => 'HTML',

        ];

        if ($reply_markup) {
            $data['reply_markup'] = $reply_markup;
        }
        if ($reply_to_message_id) {
            $data['reply_to_message_id'] = $reply_to_message_id;
        }

        return http('sendMessage', $data);
    }

    public static function forwardMessage($chatID, $from_chat_id, $message_id)
    {
        $data = [
            'chat_id'      => $chatID,
            'from_chat_id' => $from_chat_id,
            'message_id'   => $message_id,
        ];

        return http('forwardMessage', $data);
    }

    public static function copyMessage($chatID, $second_chat_id, $message_id, $reply_markup = null)
    {
        $data = [
            'chat_id'      => $chatID,
            'from_chat_id' => $second_chat_id,
            'message_id'   => $message_id,
        ];

        if ($reply_markup) {
            $data['reply_markup'] = $reply_markup;
        }

        return http('copyMessage', $data);
    }

    public static function deleteMessage($chatID, $message_id)
    {
        $data = [
            'chat_id'    => $chatID,
            'message_id' => $message_id,
        ];

        return http('deleteMessage', $data);
    }

    public static function editMessageReplyMarkup($chatID, $message_id, $reply_markup = null)
    {
        $data = [
            'chat_id'    => $chatID,
            'message_id' => $message_id,
        ];

        if ($reply_markup) {
            $data['reply_markup'] = $reply_markup;
        }

        return http('editMessageReplyMarkup', $data);
    }

    public static function inline($chatID, $second_OR_text, $message_id, $copy = false)
    {
        $reply_markup = json_encode(['inline_keyboard' => self::$keyboard]);

        if ($copy === 'edit') {
            return self::editMessageReplyMarkup($chatID, $message_id, $reply_markup);
        }
        if ($copy) {
            return self::copyMessage($chatID, $second_OR_text, $message_id, $reply_markup);
        } else {
            return self::sendMessage($chatID, $second_OR_text, $message_id, $reply_markup);
        }
    }

    public static function keyboard($chatID, $second_OR_text, $message_id, $copy = false, $resize = true, $one_time = false)
    {
        $reply_markup = [
            'keyboard' => self::$keyboard,
        ];

        if ($resize) {
            $reply_markup['resize_keyboard'] = $resize;
        }

        if ($one_time) {
            $reply_markup['one_time_keyboard'] = $one_time;
        }

        $reply_markup = json_encode($reply_markup);
        if ($copy) {
            return self::copyMessage($chatID, $second_OR_text, $message_id, $reply_markup);
        } else {
            return self::sendMessage($chatID, $second_OR_text, $message_id, $reply_markup);
        }
    }
}
