<?php

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

$BOT_TOKEN = '6877437458:AAE4VKHXOYegB7e4ylsbMfmzqprICompWWU';
define('API_URL', 'https://api.telegram.org/bot'.$BOT_TOKEN.'/');

// Include dependencies
require 'ORM/ORM.php'; // If you're using a custom ORM
require 'includes/http.php';

// Debug output function
function debug($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

// Bot class (as in your example)
class Bot
{
    private static $keyboard = [];

    public static function row($buttons)
    {
        self::$keyboard[] = $buttons;

        return new self();
    }

    public static function button($text, $callback_data = null, $url = null)
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

    public static function sendMessage($chatID, $text, $reply_markup = null)
    {
        $data = [
            'chat_id' => $chatID,
            'text'    => $text,
        ];
        if ($reply_markup) {
            $data['reply_markup'] = $reply_markup;
        }

        $response = http('sendMessage', $data);

        // Debug response
        debug($response);

        return $response;
    }

    public static function inline($chatID, $text)
    {
        $reply_markup = json_encode(['inline_keyboard' => self::$keyboard]);

        return self::sendMessage($chatID, $text, $reply_markup);
    }
}

// Capture incoming webhook update
$content = file_get_contents('php://input');
$update  = json_decode($content, true);

// Debug incoming update
debug(['Incoming Update' => $update]);

// Process the update
if ($update) {
    $message = $update['message'] ?? null;

    if ($message) {
        $chatID = $message['chat']['id'];
        $text   = $message['text'] ?? 'No text provided';

        // Debug chat data
        debug(['Chat ID' => $chatID, 'Message Text' => $text]);

        if ($text === '/start') {
            // Send a welcome message
            Bot::sendMessage($chatID, 'Welcome to the bot!');
        } else {
            // Send an acknowledgment for other messages
            Bot::sendMessage($chatID, 'You said: '.$text);
        }
    }
} else {
    // No update received
    debug(['Error' => 'No update received or invalid update format']);
}
