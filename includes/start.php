<?php

$query = [
    'customer'    => 0,
    'state'       => 0,
    'check_id'    => 0,
    'unk_connect' => 0,
];
DB::table('users')->update(['tel_id' => $chatID], $query);

$response = Bot::row([
    Bot::column('🔗 به یه ناشناس وصلم کن!', 'dd'),
])
    ->row([
        Bot::column('💌 به مخاطب خاصم وصلم کن!', 'dd'),
    ])
    ->row([
        Bot::column('لینک ناشناس من 📬', 'dd'),
        Bot::column('👥 پیام ناشناس به گروه', 'dd'),

    ])
    ->row([
        Bot::column('💡 راهنما', 'dd'),
        Bot::column('💰افزایش سکه', 'dd'),
    ])
    ->row([
        Bot::column('⚙ تنظیمات', 'dd'),
    ]);

if ($text == 'لغو جستوجو') {
    $text = 'جستجو لغو شد .
        
به منو اصلی بازگشتیم , چه کمکی از دستم بر میاد؟';
} elseif ($text == 'انصراف') {
    $text = "حله!\n\nچه کاری برات انجام بدم؟";
} elseif ($default_check == 1) {
    $text = "متوجه منظورت نشدم!\n\nبه منو اصلی برگشتیم. چه کمکی از دستم بر میاد؟";
} elseif ($default_check == 2) {
    $text = 'پیام شما ارسال شد 😊

چه کاری برات انجام بدم؟';
} elseif ($default_check == 3) {
    $text = '
حله!

آزادسازی همه بلاک‌ شده‌ها با موفقیت انجام شد !🔓


چه کاری برات انجام بدم؟‌‌
';
}
$response = Bot::keyboard($chatID, $text, $message_id);
$coin     = 30;
