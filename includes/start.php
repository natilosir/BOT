<?php

$response = Bot::row([
    Bot::column('🔗 به یه ناشناس وصلم کن!'),
])
    ->row([
        Bot::column('💌 به مخاطب خاصم وصلم کن!'),
    ])
    ->row([
        Bot::column('لینک ناشناس من 📬'),
        Bot::column('👥 پیام ناشناس به گروه'),

    ])
    ->row([
        Bot::column('💡 راهنما'),
        Bot::column('💰افزایش سکه'),
    ])
    ->row([
        Bot::column('⚙ تنظیمات'),
    ]);

$response = Bot::keyboard($chat_id, "حله!\n\nچه کاری برات انجام بدم؟", $message_id);
