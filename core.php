<?php

error_reporting(E_ALL); // Report all PHP errors
ini_set('display_errors', 1); // Display errors on screen

require 'ORM/ORM.php';
require 'http.php';
$BOT_TOKEN = '6877437458:AAE4VKHXOYegB7e4ylsbMfmzqprICompWWU';
define('api', 'https://api.telegram.org/bot'.$BOT_TOKEN.'/');

class bot
{
    public static function sendMessage($chat_id, $text, $reply_to_message_id = null)
    {
        $data_received = [
            'chat_id'             => $chat_id,
            'text'                => $text,
            'reply_to_message_id' => $reply_to_message_id,
        ];

        return http(api.'sendMessage', $data_received);
    }

    public static function forwardMessage($chat_id, $from_chat_id, $message_id)
    {
        $forwardmsg = [
            'chat_id'      => $chat_id,
            'from_chat_id' => $from_chat_id,
            'message_id'   => $message_id,
        ];

        return http(api.'forwardMessage', $forwardmsg);
    }

    public static function copyMessage($chat_id, $from_chat_id, $message_id)
    {
        $copymsg = [
            'chat_id'      => $chat_id,
            'from_chat_id' => $from_chat_id,
            'message_id'   => $message_id,
        ];
        return http(api.'copyMessage', $copymsg);
    }

    public static function deleteMessage($chat_id, $message_id)
    {
        $deletemsg = [
            'chat_id'      => $chat_id,
            'message_id'   => $message_id,
        ];
        return http(api.'deleteMessage', $deletemsg);
    }
}
