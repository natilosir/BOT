<?php

// Define the inline keyboard using a single column layout
$response = Bot::row([
    Bot::column('❓این بات چیه؟ به چه درد می‌خوره؟', 'option_1'),
])
    ->row([
        Bot::column('🔗 چطوری به یه ناشناس تصادفی وصل بشم؟', 'option_2'),
    ])
    ->row([
        Bot::column('💌 چطوری به مخاطب خاصم وصل بشم؟', 'option_3'),
    ])
    ->row([
        Bot::column('📬 چطوری پیام ناشناس دریافت کنم؟', 'option_4'),
    ])
    ->row([
        Bot::column('👥 چطوری به یه گروه پیام ناشناس بفرستم؟', 'option_5'),
    ])
    ->row([
        Bot::column('🌐 چطور لینک ناشناسمو در شبکه‌های اجتماعی بذارم؟', 'option_6'),
    ])
    ->row([
        Bot::column('🤳 لینک ناشناس به شکل QR چیه و کاربردش چیه؟', 'option_7'),
    ])
    ->row([
        Bot::column('⚙ تنظیمات برنامه ناشناس شامل چیه؟', 'option_8'),
    ])
    ->row([
        Bot::column('🚫 چطوری افراد بلاک شده رو آزاد کنم؟', 'option_9'),
    ])
    ->row([
        Bot::column('📝 چطوری اسم نمایشی خودم رو تغییر بدم؟', 'option_10'),
    ])
    ->row([
        Bot::column('⚧ چطوری جنسیت خودمو تغییر بدم؟', 'option_11'),
    ])
    ->row([
        Bot::column('📍چطوری استانم رو تغییر بدم؟', 'option_12'),
    ])
    ->row([
        Bot::column('🔕 چطوری برنامه ناشناس رو خاموش کنم؟', 'option_13'),
    ])
    ->row([
        Bot::column('📨 فیلتر پیام های دریافتی چیه و چه کاربردی داره؟', 'option_14'),
    ])
    ->row([
        Bot::column('🛡حالت چت امن در چت تصادفی چیه؟', 'option_15'),
    ]);

// Display the inline keyboard with the given helper text

// Main message text with the quote formatted
$text = "🔎 راهنما\n\n\nمن اینجام که کمکت کنم! 🤓\n <blockquote> \nبرای دسترسی به تنظیمات، کافیه دستور 👈 /settings 👉 رو لمس کنی </blockquote>\n\n\nبرای دریافت راهنمایی در مورد هر موضوع، کافیه دکمه شیشه‌ای موردنظر رو لمس کنی👇🏻";

// Send the message with reply
$response = Bot::inline($chatID, $text, $message_id);
