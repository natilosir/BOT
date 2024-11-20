<?php

error_reporting(E_ALL); // Report all PHP errors
ini_set('display_errors', 1); // Display errors on screen

$BOT_TOKEN = '6877437458:AAE4VKHXOYegB7e4ylsbMfmzqprICompWWU';
define('api', 'https://api.telegram.org/bot'.$BOT_TOKEN.'/');
require_once 'ORM/ORM.php';
require_once 'includes/http.php';

class bot
{
    private static $keyboard = []; // برای کیبورد معمولی

    public static function row($buttons)
    {
        self::$keyboard[] = $buttons;

        return new self();
    }

    public static function sendMessage($chat_id, $text, $reply_to_message_id = null, $reply_markup = null)
    {
        $data_received = [
            'chat_id'    => $chat_id,
            'text'       => $text,
            'parse_mode' => "HTML",

        ];
        if ($reply_markup) {
            $data_received['reply_markup'] = $reply_markup;
        }
        if ($reply_to_message_id) {
            $data_received['reply_to_message_id'] = $reply_to_message_id;
        }

        return http('sendMessage', $data_received);
    }

    public static function forwardMessage($chat_id, $from_chat_id, $message_id)
    {
        $forwardmsg = [
            'chat_id'      => $chat_id,
            'from_chat_id' => $from_chat_id,
            'message_id'   => $message_id,
        ];

        return http('forwardMessage', $forwardmsg);
    }

    public static function copyMessage($chat_id, $from_chat_id, $message_id)
    {
        $copymsg = [
            'chat_id'      => $chat_id,
            'from_chat_id' => $from_chat_id,
            'message_id'   => $message_id,
        ];

        return http('copyMessage', $copymsg);
    }

    public static function deleteMessage($chat_id, $message_id)
    {
        $deletemsg = [
            'chat_id'    => $chat_id,
            'message_id' => $message_id,
        ];

        return http('deleteMessage', $deletemsg);
    }

    public static function inline($chat_id, $text)
    {
        $reply_markup = json_encode(['inline_keyboard' => self::$keyboard]);

        return self::sendMessage($chat_id, $text, null, $reply_markup);
    }

    public static function keyboard($chat_id, $text, $message_id, $resize = true, $one_time = false)
    {
        $reply_markup = json_encode([
            'keyboard'          => self::$keyboard,
            'resize_keyboard'   => $resize,
            'one_time_keyboard' => $one_time,
        ]);

        return self::sendMessage($chat_id, $text, $message_id, $reply_markup);
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

    public static function displayMessages($chatId)
    {
        $messages = db::table('messages')
            ->where(['receiver_id' => $chatId, 'is_read' => 0])
            ->get();

        foreach ($messages as $msg) {
            $senderId = $msg->sender_id;
            $msgId    = $msg->id;

            $keyboard = json_encode([
                'inline_keyboard' => [
                    [['text' => 'Block', 'callback_data' => "block_$senderId"]],
                    [['text' => 'Reply', 'callback_data' => "reply_$senderId"]],
                ],
            ]);
            $msgContent = ['chat_id' => $chatId, 'reply_markup' => $keyboard];
            $caption    = '';
            self::sendMessage($chatId, $caption, $msgContent);

            // Mark message as read
            DB::Table('messages')->update(['receiver_id' => $chatId], ['is_read', 1]);

            // Notify sender of read receipt
            self::sendMessage($senderId, 'Your message has been read!');
        }
    }
}
