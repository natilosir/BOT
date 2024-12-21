<?php

Route::add(['آزادسازی بلاک شده ها 🔓', '/unblockall'], function () use ($chatID, $message_id) {
    $response = Bot::row([
        Bot::column('تایید آزادسازی بلاک‌شده‌ها 🔓', 'freee_'),
    ])->row([
        Bot::column('انصراف', 'cancle'),
    ]);
    Bot::keyboard($chatID, '
    اینجا میتونی همه کسایی که تا الان بلاک کردی رو آزاد کنی تا بتونن دوباره بهت وصل بشن یا پیام ناشناس برات بفرستن !

    برای تایید ، دکمه آزادسازی بلاک‌شده‌ها رو لمس کن 👇
    ', $message_id);
    $default_check == 3;

    exit;
});

Route::def('includes/start.php');
Route::handle($text);
