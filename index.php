<?php

require_once 'Router.php';
$user = DB::table('users');
$data = json_decode(file_get_contents('php://input'));

$message = $data->message;
if ($message) {
    $text       = $message->text;
    $chatID     = $message->chat->id;
    $fromID     = $message->from->id;
    $firstName  = $message->from->first_name;
    $lastName   = $message->from->last_name;
    $username   = $message->from->username;
    $date       = $message->date;
    $message_id = $message->message_id;
}
setupErrorHandling();

// require_once 'includes/update_user.php';
// updateUser($fromID);
bot::sendChatAction($chatID, 'typing');
require_once 'includes/help_responses.php';

Route::add(['/restart', '/start', 'لغو جستوجو', 'انصراف'], 'includes/start.php')
    ->add('/banner', 'includes/banner.php')
    ->add(['/unblockall', 'آزادسازی بلاک شده ها 🔓', 'تایید آزادسازی بلاک‌شده‌ها 🔓'], 'includes/unblockall.php')
    ->add('/helpid', 'includes/helpid.php')
    ->add('/helpcontact', 'includes/helpcontact.php')
    ->add(['/link', 'لینک ناشناس من 📬'], 'includes/mylink.php')
    ->add('/coin', 'includes/coin.php')
    ->add('/SocialLink', function () use ($chatID, $message_id, $responses) {
        $response = $responses['option_6'];
        Bot::sendMessage($chatID, $response, $message_id);
    })
    ->add(['/reset_name', 'تغییر اسم نمایشی 📝'], 'includes/resetname.php')
    ->add('/newmsg', 'includes/msg.php')
    ->add(['🔗 به یه ناشناس وصلم کن!', '🎲 جستجوی شانسی', 'حتما دختر باشه 🙍‍♀', 'حتما پسر باشه 🙎‍♂'], 'includes/unknown_connect.php')
    ->add('💌 به مخاطب خاصم وصلم کن!', 'includes/specific_connect.php')
    ->add('👥 پیام ناشناس به گروه', 'includes/group_message.php')
    ->add('💰افزایش سکه', 'includes/increase_coin.php')
    ->add('💡 راهنما', 'includes/help.php')
    ->add('⚙ تنظیمات', 'includes/settings.php')
    ->add(['من 🙍‍♀ دخترم', 'من 🙎‍♂ پسرم', 'تغییر جنسیت ⚧'], 'includes/sex.php')
    ->add('تایید آزادسازی بلاک‌شده‌ها 🔓', function () use ($fromID, $messageID, $chatID) {
        $users = DB::Table('blocks')
            ->where('subject', $fromID)
            ->delete();
        $default_check = 3;
        include_once 'includes/start.php';
        exit;
    });

Route::def('includes/default.php');
Route::handle($text);

$callbackQuery = $data->callback_query; // دریافت کل داده Callback Query
if ($callbackQuery) {
    $query_id     = $callbackQuery->id;
    $callbackData = $callbackQuery->data; // داده مربوط به دکمه
    $chatID       = $callbackQuery->message->chat->id; // شناسه چت
    $message_id   = $callbackQuery->message->message_id; // شناسه پیام اصلی
    $fromID       = $callbackQuery->from->id; // شناسه فرستنده
    $firstName    = $callbackQuery->from->first_name; // نام فرستنده
    $lastName     = isset($callbackQuery->from->last_name) ? $callbackQuery->from->last_name : null; // نام خانوادگی فرستنده
    $username     = isset($callbackQuery->from->username) ? $callbackQuery->from->username : null; // نام کاربری فرستنده
}

// ثبت روت‌ها برای گزینه‌ها
Route::add([
    'option_1',
    'option_2',
    'option_3',
    'option_4',
    'option_5',
    'option_6',
    'option_7',
    'option_8',
    'option_9',
    'option_10',
    'option_11',
    'option_12',
    'option_13',
    'option_14',
    'option_15',
], function () use ($callbackData, $responses) {
    global $chatID, $message_id;
    if (isset($responses[$callbackData])) {
        $response = $responses[$callbackData];
        Bot::sendMessage($chatID, $response, $message_id);
    }
})
    ->add(['🔓 آزادسازی', '✍ پاسخ', '🚫  گزارش کاربر'], 'includes/therestcalls.php');

Route::def('includes/def_callback.php');
Route::handle($callbackData);

processLogFile();
