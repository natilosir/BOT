<?php
error_reporting(E_ALL); // Report all PHP errors
ini_set('display_errors', 1); // Display errors on screen

//require 'ORM/ORM.php';
require 'http.php';
$BOT_TOKEN = '6877437458:AAE4VKHXOYegB7e4ylsbMfmzqprICompWWU';
define('api', 'https://api.telegram.org/bot'.$BOT_TOKEN.'/');

class bot
{
    public static function sendMessage($chat_id, $text, $reply_to_message_id = null)
    {
        $data_received = [
            'chat_id' => $chat_id,
            'text' => $text,
            'reply_to_message_id' => $reply_to_message_id,
        ];


        return http(api.'sendMessage', $data_received );

    }
}

print_r(
    bot::sendMessage($chatid,$text)
);
