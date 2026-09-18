<?php

$configFile = __DIR__ . '/../../../config.php';

if ( file_exists($configFile) ) {
    echo "✅ پیکربندی قبلاً انجام شده است. برای تغییر مجدد، فایل config.php را حذف کنید.\n";
    exit(0);
}

$sourceDir      = __DIR__ . '/../telegram-bot-sdk/';
$destinationDir = __DIR__ . '/../../../';

if ( is_dir($sourceDir) ) {
    $files = scandir($sourceDir);

    foreach ( $files as $file ) {
        if ( $file === '.' || $file === '..' ) {
            continue;
        }

        rename($sourceDir . $file, $destinationDir . $file);
    }

    rmdir($sourceDir);
}

if ( !is_dir($sourceDir) ) {
    function prompt( string $message ): string {
        echo $message . ': ';
        $handle = fopen('php://stdin', 'r');
        $input  = fgets($handle);
        fclose($handle);

        return trim((string) $input);
    }

    function promptDefault( string $message, string $default, string $yellow, string $green, string $reset ): string {
        $value = prompt($green . $message . $reset . ' [if empty: ' . $yellow . $default . $reset . ']');

        return $value === '' ? $default : $value;
    }

    $yellow = "\033[33m";
    $green  = "\033[32m";
    $reset  = "\033[0m";

    $botToken = prompt($green . 'Please enter your bot token API' . $reset);

    $dbHost = promptDefault('Please enter your database host', 'localhost', $yellow, $green, $reset);

    $dbUser = promptDefault('Please enter your database username', 'root', $yellow, $green, $reset);

    $dbName = promptDefault('Please enter your database name', 'natilos', $yellow, $green, $reset);

    $dbDriver = prompt($green . 'Please enter database driver' . $reset . ' [if empty: ' . $yellow . 'mysql' . $reset . ']');

    $dbPort = prompt($green . 'Please enter database port' . $reset . ' [if empty: ' . $yellow . '3306' . $reset . ']');

    $dbPassword = prompt($green . 'Please enter database password' . $reset . ' [if empty: ' . $yellow . "''" . $reset . ']');

    $dbCharset = prompt($green . 'Please enter database charset' . $reset . ' [if empty: ' . $yellow . 'utf8mb4' . $reset . ']');

    $dbCollation = prompt($green . 'Please enter database collation' . $reset . ' [if empty: ' . $yellow . 'utf8mb4_unicode_ci' . $reset . ']');

    $dbPrefix = prompt($green . 'Please enter database prefix' . $reset . ' [if empty: ' . $yellow . "''" . $reset . ']');

    $dbStrict = prompt($green . 'Please enter database strict mode (true/false)' . $reset . ' [if empty: ' . $yellow . 'true' . $reset . ']');

    $databaseLines = [];

    $databaseLines[] = "        'host'     => " . var_export($dbHost, true) . ",";
    $databaseLines[] = "        'user'     => " . var_export($dbUser, true) . ",";
    $databaseLines[] = "        'database' => " . var_export($dbName, true) . ",";

    if ( $dbDriver !== '' ) {
        $databaseLines[] = "        'driver'    => " . var_export($dbDriver, true) . ",";
    }

    if ( $dbPort !== '' ) {
        if ( !ctype_digit($dbPort) ) {
            fwrite(STDERR, "\n❌ Invalid database port.\n");
            exit(1);
        }

        $databaseLines[] = "        'port'      => " . (int) $dbPort . ",";
    }

    if ( $dbPassword !== '' ) {
        $databaseLines[] = "        'password'  => " . var_export($dbPassword, true) . ",";
    }

    if ( $dbCharset !== '' ) {
        $databaseLines[] = "        'charset'   => " . var_export($dbCharset, true) . ",";
    }

    if ( $dbCollation !== '' ) {
        $databaseLines[] = "        'collation' => " . var_export($dbCollation, true) . ",";
    }

    if ( $dbPrefix !== '' ) {
        $databaseLines[] = "        'prefix'    => " . var_export($dbPrefix, true) . ",";
    }

    if ( $dbStrict !== '' ) {
        $strict = strtolower($dbStrict);

        if ( !in_array($strict, [ 'true', 'false', '1', '0' ], true) ) {
            fwrite(STDERR, "\n❌ Strict mode must be true or false.\n");
            exit(1);
        }

        $databaseLines[] = "        'strict'    => " . ( in_array($strict, [
                'true',
                '1',
            ], true) ? 'true' : 'false' ) . ",";
    }

    $databaseConfig = implode("\n", $databaseLines);

    $configContent = <<<'EOD'
        <?php
        
        return [
            'timezone' => 'Asia/Tehran',
            'locale'   => 'fa',
            'calendar' => 'jalali',
        
            'bot' => [
                'token' => %BOT_TOKEN%,
            ],
        
            'database' => [
        %DATABASE_CONFIG%
            ],
        ];
        EOD;

    $configContent = str_replace([
        '%BOT_TOKEN%',
        '%DATABASE_CONFIG%',
    ], [
        var_export($botToken, true),
        $databaseConfig,
    ], $configContent);

    if ( file_put_contents($configFile, $configContent) === false ) {
        fwrite(STDERR, "\n❌ Could not create config.php.\n");
        exit(1);
    }

    echo "\n✅ The application is ready to run. Please read the documentation.\n";
}
else {
    echo "ERROR: Source directory not found.\n";
}
