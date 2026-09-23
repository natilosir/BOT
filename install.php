<?php
$yellow = "\033[33m";
$green  = "\033[32m";
$reset  = "\033[0m";

$configFile = __DIR__ . '/../../../config.php';
$sourceDir      = __DIR__ . '/../telegram-bot-sdk/';
$destinationDir = __DIR__ . '/../../../';

if ( file_exists($configFile) or !is_dir($sourceDir) ) {
    echo $green . "✅ Configuration already completed. \n" . $reset;
    exit(0);
}

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

    /**
     * Build connection lines.
     *
     * @param array<string, mixed> $connection        Raw values (may be missing keys)
     * @param bool                 $fillDefaults      Whether to fill missing keys with defaults
     * @param string               $indent
     * @return list<string>
     */
    function buildDatabaseConnectionLines( array $connection, bool $fillDefaults, string $indent = '                ' ): array {
        $orderedKeys = [ 'driver', 'host', 'port', 'database', 'user', 'password', 'charset', 'collation', 'prefix', 'strict' ];

        $defaults = [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'port'      => 3306,
            'database'  => '',
            'user'      => '',
            'password'  => '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
        ];

        $lines = [];

        foreach ( $orderedKeys as $key ) {
            $has = array_key_exists($key, $connection);

            if ( !$has && !$fillDefaults ) {
                continue;
            }

            $value = $has ? $connection[$key] : $defaults[$key];

            if ( $key === 'port' ) {
                $lines[] = $indent . "'{$key}' => " . (int) $value . ",";
            } elseif ( $key === 'strict' ) {
                $lines[] = $indent . "'{$key}' => " . ( $value ? 'true' : 'false' ) . ",";
            } else {
                $lines[] = $indent . "'{$key}' => " . var_export($value, true) . ",";
            }
        }

        return $lines;
    }

    $botDriver = strtolower(promptDefault('Please select bot driver (telegram/bale)', 'telegram', $yellow, $green, $reset));

    if ( !in_array($botDriver, [ 'telegram', 'bale' ], true) ) {
        fwrite(STDERR, "\n❌ Bot driver must be telegram or bale.\n");
        exit(1);
    }

    $telegramBotToken = prompt($green . 'Please enter Telegram bot token (leave empty if unused)' . $reset);
    $baleBotToken     = prompt($green . 'Please enter Bale bot token (leave empty if unused)' . $reset);

    $selectedToken = $botDriver === 'telegram' ? $telegramBotToken : $baleBotToken;
    if ( $selectedToken === '' ) {
        fwrite(STDERR, "\n❌ The selected default driver [{$botDriver}] must have a token.\n");
        exit(1);
    }

    $telegramWebhookSecret = $telegramBotToken !== '' ? bin2hex(random_bytes(24)) : '';

    // ---------------------------------------------------------------------
    // Database connections
    // ---------------------------------------------------------------------

    $connectionCount = (int) promptDefault('Please enter number of database connections', '1', $yellow, $green, $reset);

    if ( $connectionCount < 1 ) {
        $connectionCount = 1;
    }

    $databaseConnections = [];

    for ( $i = 0; $i < $connectionCount; $i++ ) {
        $defaultName = $i === 0 ? 'default' : 'database' . ( $i + 1 );

        echo "\n" . $green . "Database connection #" . ( $i + 1 ) . $reset . "\n";

        $connectionName = promptDefault('Please enter connection name', $defaultName, $yellow, $green, $reset);
        $connectionName = preg_replace('/[^a-zA-Z0-9_]/', '_', $connectionName);

        if ( $connectionName === '' || $connectionName === null ) {
            $connectionName = $defaultName;
        }

        while ( isset($databaseConnections[$connectionName]) ) {
            $connectionName .= '_' . ( $i + 1 );
        }

        $connection = [];

        // ---- Required fields ----
        $dbHost = prompt($green . 'Please enter your database host' . $reset);
        if ( $dbHost === '' ) {
            fwrite(STDERR, "\n❌ Database host is required.\n");
            exit(1);
        }
        $connection['host'] = $dbHost;

        $dbUser = prompt($green . 'Please enter your database username' . $reset);
        if ( $dbUser === '' ) {
            fwrite(STDERR, "\n❌ Database username is required.\n");
            exit(1);
        }
        $connection['user'] = $dbUser;

        $dbName = prompt($green . 'Please enter your database name' . $reset);
        if ( $dbName === '' ) {
            fwrite(STDERR, "\n❌ Database name is required.\n");
            exit(1);
        }
        $connection['database'] = $dbName;

        // ---- Optional fields (omit if empty) ----
        $dbDriver = prompt($green . 'Please enter database driver' . $reset . ' [if empty: ' . $yellow . 'mysql' . $reset . ']');
        if ( $dbDriver !== '' ) {
            $connection['driver'] = $dbDriver;
        }

        $dbPort = prompt($green . 'Please enter database port' . $reset . ' [if empty: ' . $yellow . '3306' . $reset . ']');
        if ( $dbPort !== '' ) {
            if ( !ctype_digit($dbPort) ) {
                fwrite(STDERR, "\n❌ Invalid database port.\n");
                exit(1);
            }
            $connection['port'] = (int) $dbPort;
        }

        $dbPassword = prompt($green . 'Please enter database password' . $reset . ' [if empty: ' . $yellow . "''" . $reset . ']');
        if ( $dbPassword !== '' ) {
            $connection['password'] = $dbPassword;
        }

        $dbCharset = prompt($green . 'Please enter database charset' . $reset . ' [if empty: ' . $yellow . 'utf8mb4' . $reset . ']');
        if ( $dbCharset !== '' ) {
            $connection['charset'] = $dbCharset;
        }

        $dbCollation = prompt($green . 'Please enter database collation' . $reset . ' [if empty: ' . $yellow . 'utf8mb4_unicode_ci' . $reset . ']');
        if ( $dbCollation !== '' ) {
            $connection['collation'] = $dbCollation;
        }

        $dbPrefix = prompt($green . 'Please enter database prefix' . $reset . ' [if empty: ' . $yellow . "''" . $reset . ']');
        if ( $dbPrefix !== '' ) {
            $connection['prefix'] = $dbPrefix;
        }

        $dbStrict = prompt($green . 'Please enter database strict mode (true/false)' . $reset . ' [if empty: ' . $yellow . 'true' . $reset . ']');
        if ( $dbStrict !== '' ) {
            $strict = strtolower($dbStrict);
            if ( !in_array($strict, [ 'true', 'false', '1', '0' ], true) ) {
                fwrite(STDERR, "\n❌ Strict mode must be true or false.\n");
                exit(1);
            }
            $connection['strict'] = in_array($strict, [ 'true', '1' ], true);
        }

        $databaseConnections[$connectionName] = $connection;
    }

    $firstConnectionName = array_key_first($databaseConnections);

    $databaseLines   = [];
    $databaseLines[] = "        'default' => " . var_export($firstConnectionName, true) . ",";
    $databaseLines[] = "        'connections' => [";

    $index = 0;

    foreach ( $databaseConnections as $name => $connection ) {
        if ( $index === 0 ) {
            $databaseLines[] = "            " . var_export($name, true) . " => [";
            foreach ( buildDatabaseConnectionLines($connection, false) as $line ) {
                $databaseLines[] = $line;
            }
            $databaseLines[] = "            ],";
        } else {
            $databaseLines[] = "            /*";
            $databaseLines[] = "            " . var_export($name, true) . " => [";
            foreach ( buildDatabaseConnectionLines($connection, true) as $line ) {
                $databaseLines[] = $line;
            }
            $databaseLines[] = "            ],";
            $databaseLines[] = "            */";
        }

        $index++;
    }

    $databaseLines[] = "        ],";

    $databaseConfig = implode("\n", $databaseLines);

    $configContent = <<<'EOD'
<?php

return [

    'timezone' => 'Asia/Tehran',
    'locale'   => 'fa',
    'calendar' => 'jalali',

    'bot' => [
        'default' => %BOT_DRIVER%,

        'drivers' => [
            'telegram' => [
                'token'    => %TELEGRAM_BOT_TOKEN%,
                'base_url' => 'https://api.telegram.org',

                'webhook' => [
                    'url'          => '/webhook/telegram',
                    'secret_token' => %TELEGRAM_WEBHOOK_SECRET%,
                ],
            ],

            'bale' => [
                'token'    => %BALE_BOT_TOKEN%,
                'base_url' => 'https://tapi.bale.ai',

                'webhook' => [
                    'url' => '/webhook/bale',
                ],
            ],
        ],
    ],

    'database' => [
%DATABASE_CONFIG%
    ],

];
EOD;

    $configContent = str_replace([
        '%BOT_DRIVER%',
        '%TELEGRAM_BOT_TOKEN%',
        '%BALE_BOT_TOKEN%',
        '%TELEGRAM_WEBHOOK_SECRET%',
        '%DATABASE_CONFIG%',
    ], [
        var_export($botDriver, true),
        var_export($telegramBotToken, true),
        var_export($baleBotToken, true),
        var_export($telegramWebhookSecret, true),
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
