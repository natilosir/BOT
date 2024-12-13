<?php

// Define the inline keyboard using a single column layout
$reponde = Bot::row([[
    'text' => ' ➕ اضافه کردن ربات به گروه ➕',
    'url'  => 'https://t.me/Thekodamabot?startgroup=true',
],
])
    ->row([
        [
            'text'          => '💭 آپدیت لیست گروه‌ها 💭',
            'callback_data' => 'updategp',
        ]]);

function handleAddGroup($userId, $groupName)
{
    // Example: Save this group name to your database for this user
    // Assuming you have a `user_groups` table to track user added groups
    $db->query('INSERT INTO user_groups (user_id, group_name) VALUES (?, ?)', [$userId, $groupName]);
}

// Display the inline keyboard with the given helper text
$reponde = Bot::inline($chatID, "گروهی که میخوای این پیام به صورت ناشناس بهش ارسال بشه رو انتخاب کن !\n\nاگه اسم گروهت توی صفحه شیشه‌ای زیر نیست،\n گزینه ➕ اضافه کردن ربات به گروه ➕ رو لمس کن ، ربات رو توی گروهی که میخوای پیام ناشناس بفرستی عضو کن و بعد آپدیت لیست گروه ها رو لمس کن", $message_id);
