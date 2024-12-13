<?php

$number = DB::table('users')->where('tel_id', $fromID)->first()->coin;
// Define the inline keyboard using a single column layout
$response = Bot::row([
    Bot::column('خرید 100 سکه - 20 هزارتومان', '100'),
])
    ->row([
        Bot::column('خرید 220 سکه - 40 هزار تومان', '220'),
    ])
    ->row([
        Bot::column('خرید 440 سکه - 80 هزار تومان', '440'),
    ]);

// Display the inline keyboard with the given helper text

$text     = "<b>💰 سکه فعلی شما : $number عدد </b>\n\nـــــــــــــــــــــــــــــــ‌ــــــــــــــــــــ\n\n<b>❓روش های بدست آوردن سکه چیه ؟</b>\n\n<blockquote>1️⃣ روش اول :</blockquote>\nمعرفی ربات به دوستان\n\nبنر مخصوص خودت رو به دوستات فوروارد کن و به ازای هر کاربر جدیدی که از طرف تو وارد ربات میشه 30 سکه جدید بگیر!😀\n\n🔺برای دریافت بنر دستور /banner رو لمس کن\n\n<blockquote>2⃣ روش دوم :</blockquote>\nورود کاربرای جدید با لینک پیام ناشناست\n\nاگه یه کاربر جدید از طریق لینک ناشناست وارد ربات بشه تا بهت پیام ناشناس بده، 3 سکه جدید دریافت میکنی!👌\n\n🔺 برای دریافت لینک ناشناست دستور /link رو لمس کن.\n\n<blockquote>3⃣ روش سوم :</blockquote>\nسکه روزانه رایگان :\n\n🎊 میتونی روزی یکبار دستور /coin رو لمس کنی و  روزی 2 سکه رایگان دریافت کنی!🪙\n\n<blockquote>4⃣ روش چهارم :</blockquote>\nخرید با کارت بانکی :\n\nبرای خرید سکه یکی از تعرفه‌های زیر رو انتخاب کن👇";
$response = Bot::inline($chatID, $text, $message_id);
