<?php

// ===========================
// 1. بررسی وجود فایل کانفیگ (یک‌بار اجرا)
// ===========================
$configFile = __DIR__ . '/../../../config.php';
if (file_exists($configFile)) {
    echo "✅ پیکربندی قبلاً انجام شده است. برای تغییر مجدد، فایل config.php را حذف کنید.\n";
    exit(0);
}

// ===========================
// 2. انتقال فایل‌ها (بخش قبلی)
// ===========================
$sourceDir      = __DIR__.'/../telegram-bot-sdk/';
$destinationDir = __DIR__.'/../../../';

if (is_dir($sourceDir)) {
    $files = scandir($sourceDir);

    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $sourceFile      = $sourceDir.$file;
            $destinationFile = $destinationDir.$file;

            if (is_dir($sourceFile)) {
                rename($sourceFile, $destinationFile);
            } else {
                rename($sourceFile, $destinationFile);
            }
        }
    }

    rmdir($sourceDir);
}

// ===========================
// 3. گرفتن اطلاعات (تنها در صورتی که پوشه منبع وجود نداشته باشد)
// ===========================
if (! is_dir($sourceDir)) {
    // تابع ورودی با پشتیبانی از readline
    function prompt($message)
    {
        echo $message . ': ';
        $handle = fopen('php://stdin', 'r');
        $input = fgets($handle);
        fclose($handle);
        return trim($input);
    }

    // ANSI color codes
    $yellow = "\033[33m";
    $green  = "\033[32m";
    $reset  = "\033[0m";

    // دریافت اطلاعات
    $botToken = prompt($green.'Please enter your bot token API'.$reset);
    $dbHost   = prompt($green.'Please enter your database host'.$reset.' [if empty: '.$yellow.'localhost'.$reset.']');
    if (empty($dbHost)) {
        $dbHost = 'localhost';
    }

    $dbUser = prompt($green.'Please enter your database username'.$reset.' [if empty: '.$yellow.'root'.$reset.']');
    if (empty($dbUser)) {
        $dbUser = 'root';
    }

    $dbPassword = prompt($green.'Please enter your database password'.$reset);
    $dbName     = prompt($green.'Please enter your database name'.$reset);

    // ساخت محتوای config.php
    $configContent = <<<'EOD'
<?php

return [

/*
|--------------------------------------------------------------------------
| Bot Configuration
|--------------------------------------------------------------------------
|
| This section contains the configuration for the bot.
| You need to provide the token to connect to the bot API.
|
*/

'bot' => [
    'token' => '%BOT_TOKEN%', 
],

/*
|--------------------------------------------------------------------------
| Database Configuration
|--------------------------------------------------------------------------
|
| This section contains the configuration for the database connection.
| You need to provide the host, username, password, and database name
| to connect to your database.
|
*/

'database' => [
    'host' => '%DB_HOST%',
    'user' => '%DB_USER%',
    'password' => '%DB_PASSWORD%',
    'database' => '%DB_NAME%',
],

];
EOD;

    $replacements = [
        '%BOT_TOKEN%'   => $botToken,
        '%DB_HOST%'     => $dbHost,
        '%DB_USER%'     => $dbUser,
        '%DB_PASSWORD%' => $dbPassword,
        '%DB_NAME%'     => $dbName,
    ];

    foreach ($replacements as $placeholder => $value) {
        $configContent = str_replace($placeholder, $value, $configContent);
    }

    file_put_contents($configFile, $configContent);

    echo "\n✅ The application is ready to run. Please read the documentation.\n";
} else {
    echo 'ERROR: Source directory not found.'."\n";
}