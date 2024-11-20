<?php

require_once 'core.php';

// Read the raw POST data
$data = json_decode(file_get_contents('php://input'));

$message    = $data->message;
$text       = $data->message->text;
$chat_id    = $data->message->chat->id;
$from_id    = $data->message->from->id;
$first_name = $data->message->from->first_name;
$last_name  = $data->message->from->last_name;
$username   = $data->message->from->username;
$date       = $data->message->date;
$message_id = $data->message->message_id;

require_once 'includes/update_user.php';
updateUser($from_id);

switch ($text) {
    case '/start':
        include_once 'includes/start.php';
        break;

    case '🔗 به یه ناشناس وصلم کن!':
        include_once 'includes/unknown_connect.php';
        break;

    case '💌 به مخاطب خاصم وصلم کن!':
        include_once 'includes/specific_connect.php';
        break;

    case '👥 پیام ناشناس به گروه':
        include_once 'includes/group_message.php';
        break;

    case 'لینک ناشناس من 📬':
        include_once 'includes/mylink.php';
        break;

    case '💰افزایش سکه':
        include_once 'includes/increase_coin.php';
        break;

    case '💡 راهنما':
        include_once 'includes/help.php';
        break;

    case '⚙ تنظیمات':
        include_once 'includes/settings.php';
        break;

    default:
        Bot::sendMessage($chat_id, "متوجه منظورت نشدم!\n\nبه منو اصلی برگشتیم. چه کمکی از دستم بر میاد؟", $message_id);
        break;
}
