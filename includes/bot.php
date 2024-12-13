<?php

error_reporting(E_ALL); // Report all PHP errors
ini_set('display_errors', 1); // Display errors on screen

$BOT_TOKEN = '6877437458:AAE4VKHXOYegB7e4ylsbMfmzqprICompWWU';
define('api', 'https://api.telegram.org/bot'.$BOT_TOKEN.'/');
require 'ORM/ORM.php';
require 'includes/http.php';

require 'db_config.php';

class Bot
{
    private static $keyboard = []; // For regular keyboard

    public static function row($buttons)
    {
        self::$keyboard[] = $buttons; // Add row of buttons to the keyboard

        return new self();
    }

    public static function sendMessage($chatID, $text, $reply_to_message_id = null, $reply_markup = null)
    {
        $data_received = [
            'chat_id' => $chatID,
            'text'    => $text,
        ];
        if ($reply_markup) {
            $data_received['reply_markup'] = $reply_markup;
        }
        if ($reply_to_message_id) {
            $data_received['reply_to_message_id'] = $reply_to_message_id; // Set the reply_to_message_id
        }

        return http('sendMessage', $data_received);
    }

    public static function keyboard($chatID, $text, $reply_to_message_id = null, $resize = true, $one_time = false)
    {
        $reply_markup = json_encode([
            'keyboard'          => self::$keyboard,
            'resize_keyboard'   => $resize,
            'one_time_keyboard' => $one_time,
        ]);

        return self::sendMessage($chatID, $text, $reply_to_message_id, $reply_markup);
    }

    public static function button($text)
    {
        return ['text' => $text]; // Regular keyboard button
    }
}

// Function to handle button actions
function handleButtonAction($text, $chatID, $message_id)
{
    switch ($text) {
        case '🔗 به یه ناشناس وصلم کن!':
            include_once 'path_to_file/unknown_connect.php';
            break;

        case '💌 به مخاطب خاصم وصلم کن!':
            include_once 'path_to_file/specific_connect.php';
            break;

        case '👥 پیام ناشناس به گروه':
            include_once 'path_to_file/group_message.php';
            break;

        case 'لینک ناشناس من 📬':
            include_once 'mylink.php';
            break;

        case '💰افزایش سکه':
            include_once 'path_to_file/increase_coin.php';
            break;

        case '💡 راهنما':
            include_once 'path_to_file/help.php';
            break;

        case '⚙ تنظیمات':
            include_once 'path_to_file/settings.php';
            break;

        default:
            Bot::sendMessage($chatID, "متوجه منظورت نشدم!\n\nبه منو اصلی برگشتیم. چه کمکی از دستم بر میاد؟", $message_id);
            break;
    }
}

// Process webhook updates
$content = file_get_contents('php://input'); // Get incoming update
$update  = json_decode($content, true); // Decode the JSON update

if (isset($update['message'])) {
    $chatID     = $update['message']['chat']['id']; // Dynamic chat ID of the user
    $text       = $update['message']['text']; // Command or message text
    $message_id = $update['message']['message_id']; // ID of the user's message

    if ($text === '/start') { // If the user sends the `/start` command
        $response = Bot::row([
            Bot::button('🔗 به یه ناشناس وصلم کن!'),
        ])
            ->row([
                Bot::button('💌 به مخاطب خاصم وصلم کن!'),
            ])
            ->row([
                Bot::button('لینک ناشناس من 📬'),
                Bot::button('👥 پیام ناشناس به گروه'),

            ])
            ->row([
                Bot::button('💡 راهنما'),
                Bot::button('💰افزایش سکه'),
            ])
            ->row([
                Bot::button('⚙ تنظیمات'),
            ]);

        $welcomeText = "حله!\n\nچه کاری برات انجام بدم؟";
        Bot::keyboard($chatID, $welcomeText, $message_id); // Reply with a keyboard
    } else {
        handleButtonAction($text, $chatID, $message_id); // Handle button actions
    }
}
