<?php

$response = Bot::row([
    Bot::column('آزادسازی بلاک شده ها 🔓', 'free_'),
    Bot::column('تغییر اسم نمایشی 📝', 'changename_'),
])
->row([
    Bot::column('فیلتر پیام‌های دریافتی 📥', 'income_'),
    Bot::column('تغییر جنسیت ⚧', 'gender_'),
])
->row([
    Bot::column('خاموش کردن برنامه ناشناس 🔇', 'off_'),
])
->row([
    Bot::column('انصراف', 'cancle'),
]);

// Send the main menu to the user
Bot::keyboard($chatID, "منو تنظیمات :", $message_id);

// Switch to handle callback data actions
switch ($callbackData) {
    case 'free_':
        // Logic for freeing blocked users
        $response = $responses['free_']; // Example: could send a message or take an action
        break;

    case 'changename_':
        // Handle changing the display name
        include_once 'include/resetname.php'; // Including the script to handle name change
        break;

    case 'فیلتر پیام‌های دریافتی 📥':
        // Handle message filter selection
        $content = "
از اینجا می‌تونی انتخاب کنی که فرمت پیام‌های ناشناس دریافتی به چه صورتی باشه.

مثلاً اگر می‌خوای فقط پیام‌های متنی دریافت کنی، بقیه قسمت‌ها رو غیرفعال کن. اینطوری کسی نمی‌تونه پیام ناشناس به صورت عکس، ویدیو، موزیک و ... برات ارسال کنه.

دقت کن که بعد از 30 روز به تنظیمات اولیه برمی‌گرده و همه فعال می‌شن.

برای تغییر روی هر کدوم کلیک کن 👇

";
        
        // Send options for message format (text, image, audio, etc.)
        $reponde = Bot::row([
            Bot::column('متنی', 'option_1'),
            Bot::column('چک', 'option_2'),
        ])
        ->row([
            Bot::column('صوتی', 'option_3'),
            Bot::column('چک', 'option_4'),
        ])
        ->row([
            Bot::column('تصویری', 'option_12'),
            Bot::column('چک', 'option_12'),
        ])
        ->row([
            Bot::column('فایلی', 'option_13'),
            Bot::column('چک', 'option_14'),
        ])
        ->row([
            Bot::column('بازگشت به تنظیمات اولیه', 'option_15'),
        ]);

        // Send the inline keyboard with the options to the user
        $reponde=Bot::inline($chatID, $content, $message_id);
        break;

    case 'gender_':
        // Logic for changing gender
        $response = $responses['gender_']; // Example: could send a message or perform a gender change
        break;

    case 'off_':
        // Logic for turning off the anonymous program
        $response = $responses['off_']; // Example: could disable or turn off a setting
        break;

    default:
        // Default case if callback data doesn't match any known value
        include_once 'includes/def_callback.php'; // Include the default callback handler
        break;
}