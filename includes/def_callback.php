<?php

// تجزیه callbackData به اجزا
$dataParts = explode('_', $callbackData);

// اجزای callback_data
$action                   = isset($dataParts[0]) ? $dataParts[0] : null; // قسمت اول (نوع اقدام: reply یا block)
$receiver                 = isset($dataParts[1]) ? $dataParts[1] : null; // قسمت دوم (گیرنده)
$sender                   = isset($dataParts[2]) ? $dataParts[2] : null; // قسمت سوم (فرستنده)
$message_id_from_callback = isset($dataParts[3]) ? $dataParts[3] : null; // قسمت چهارم (شناسه پیام)

// تعریف اکشن reply
Route::add('reply', function () use ($receiver, $sender, $chatID, $message_id) {
    $the_user    = DB::table('users')->where('tel_id', $receiver)->first();
    $second_user = DB::table('users')->where('tel_id', $sender)->first();

    DB::table('users')->where('id', $the_user->id)->update(['customer' => $second_user->id]);

    Bot::row([Bot::column('انصراف')]);
    Bot::keyboard($chatID, '☝️ در حال پاسخ دادن به فرستنده این پیام هستی ... ؛ منتظریم بفرستی :)', $message_id);
});

// تعریف اکشن block
Route::add('block', function () use ($receiver, $sender, $message_id_from_callback, $message_id, $chatID, $query_id) {
    $find = DB::table('blocks')
        ->where('subject', $receiver)
        ->where('object', $sender)
        ->first();

    if ($find) {
        Bot::alert($query_id, 'already blocked');
    } else {
        $blockedUser = [
            'subject' => $receiver,
            'object'  => $sender,
        ];

        DB::table('blocks')->insert($blockedUser);

        Bot::row([
            Bot::column('🔓 آزادسازی', 'unblock_'.$receiver.'_'.$sender.'_'.$message_id_from_callback),
            Bot::column('✍ پاسخ', 'reply_'.$receiver.'_'.$sender.'_'.$message_id_from_callback),
        ])->row([
            Bot::column('🚫 گزارش کاربر', 'report_'.$receiver.'_'.$sender.'_'.$message_id_from_callback),
        ]);

        Bot::alert($query_id, '🚫 بلاک شد !');

        $tel_response = Bot::inline($chatID, null, $message_id, 'edit');
        if (isset($tel_response->ok)) {
            // Handle success if needed
        }
    }
});

// تعریف اکشن unblock
Route::add('unblock', function () use ($receiver, $sender, $query_id) {
    $deleted = DB::table('blocks')
        ->where('subject', $receiver)
        ->where('object', $sender)
        ->delete();

    if ($deleted) {
        Bot::alert($query_id, 'با موفقیت آزاد شد');
    }
});

// تعریف اکشن report
Route::add('report', function () use ($chatID, $receiver, $sender, $message_id) {
    Bot::row([Bot::column('📢 ارسال تبلیغات یا پیام‌های تکراری')])
        ->row([Bot::column('🚫 ارسال محتوای غيراخلاقی یا خشونت‌آمیز')])
        ->row([Bot::column('⚠ مزاحمت - نواع تهدید یا توهین - نشر اکاذیب')])
        ->row([Bot::column('📵 پخش شماره موبایل یا اطلاعات شخصی دیگران')])
        ->row([Bot::column('❓ موارد دیگر')])
        ->row([Bot::column('انصراف')]);

    Bot::keyboard(
        $chatID,
        '
پیام سیستم:

چرا می‌خوای گزارشش کنی؟

',
        null,
        false,
        true,
        true
    );

    // Example logic for handling one type of report
    Route::add('📢 ارسال تبلیغات یا پیام‌های تکراری', function () use ($receiver, $sender, $message_id, $chatID) {
        // DB::table('reports')->insert([
        //     'report_type' => 'advertising',
        //     'subject'     => $receiver,
        //     'object'      => $sender,
        //     'message_id'  => $message_id,
        // ]);

        $msg = [
            'sender'     => $fromID,
            'receiver'   => $second_user->tel_id,
            'message_id' => $message_id,
        ];
        Bot::sendMessage($receiver, '
گزارش شما با موفقیت ثبت شد ✅

با بیشتر شدن تعداد گزارشات  ، این کاربر بصورت خودکار توسط سیستم مسدود شده و دیگر قادر به استفاده از ربات نخواهد بود.
', $chatID);
        Bot::sendMessage($sender, '
پیام سیستم : گزارش تخلف

نوع تخلف :

{$text}

دقت کن، اگه تعداد گزارش‌ها بیشتر بشه ، سیستم بصورت خودکار مسدودت میکنه و دیگه نمیتونی از ربات استفاده کنی.

');
    });
});

// تنظیم مقدار پیش‌فرض (در صورت نیاز)
Route::def('default_action.php');

// پردازش اکشن
Route::handle($action);
