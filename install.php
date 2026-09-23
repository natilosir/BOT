<?php
$yellow = "\033[33m";
$green  = "\033[32m";
$reset  = "\033[0m";

$configFile = __DIR__ . '/../../../config.php';

if ( file_exists($configFile) ) {
    echo $green . "✅ Configuration already completed. \n" . $reset;
    exit(0);
}

function prompt( string $message ): string {
    echo $message . ': ';
    $handle = fopen('php://stdin', 'r');
    $input  = fgets($handle);
    fclose($handle);

    return trim((string) $input);
}

function promptDefault( string $message, string $default, string $green, string $yellow, string $reset ): string {
    $value = prompt($green . $message . $reset . ' [' . $yellow . $default . $reset . ']');

    return $value === '' ? $default : $value;
}

function promptRequired( string $message, string $green, string $reset, string $errorMessage ): string {
    while ( true ) {
        $value = prompt($green . $message . $reset);
        if ( $value !== '' ) {
            return $value;
        }
        fwrite(STDERR, "\n❌ " . $errorMessage . "\n");
    }
}

function promptOptional( string $message, string $default, string $green, string $yellow, string $reset ): ?string {
    $value = prompt($green . $message . $reset . ' [' . $yellow . $default . $reset . ']');

    return $value === '' ? null : $value;
}

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

// ---------------------------------------------------------------------
// Timezone
// ---------------------------------------------------------------------
$timezone = promptDefault('Please enter your timezone', 'Asia/Tehran', $green, $yellow, $reset);

// ---------------------------------------------------------------------
// Bot driver
// ---------------------------------------------------------------------
while ( true ) {
    $botDriver = strtolower(promptDefault('Please select bot driver (telegram/bale)', 'telegram', $green, $yellow, $reset));

    if ( in_array($botDriver, [ 'telegram', 'bale' ], true) ) {
        break;
    }

    fwrite(STDERR, "\n❌ Bot driver must be telegram or bale.\n");
}

// ---------------------------------------------------------------------
// Bot tokens
// ---------------------------------------------------------------------
while ( true ) {
    $telegramBotToken = prompt($green . 'Please enter Telegram bot token (leave empty if unused)' . $reset);
    $baleBotToken     = prompt($green . 'Please enter Bale bot token (leave empty if unused)' . $reset);

    $selectedToken = $botDriver === 'telegram' ? $telegramBotToken : $baleBotToken;

    if ( $selectedToken !== '' ) {
        break;
    }

    fwrite(STDERR, "\n❌ The selected default driver [{$botDriver}] must have a token.\n");
}

// secret_token همیشه خالی
$telegramWebhookSecret = '';

// ---------------------------------------------------------------------
// Database connections
// ---------------------------------------------------------------------

while ( true ) {
    $connectionCount = (int) promptDefault('Please enter number of database connections', '1', $green, $yellow, $reset);

    if ( $connectionCount >= 1 ) {
        break;
    }

    fwrite(STDERR, "\n❌ Number of connections must be at least 1.\n");
}

$databaseConnections = [];

for ( $i = 0; $i < $connectionCount; $i++ ) {
    $defaultName = $i === 0 ? 'mysql' : 'database' . ( $i + 1 );

    echo "\n" . $green . "Database connection #" . ( $i + 1 ) . $reset . "\n";

    $connectionName = promptDefault('Please enter connection name', $defaultName, $green, $yellow, $reset);
    $connectionName = preg_replace('/[^a-zA-Z0-9_]/', '_', $connectionName);

    if ( $connectionName === '' || $connectionName === null ) {
        $connectionName = $defaultName;
    }

    while ( isset($databaseConnections[$connectionName]) ) {
        $connectionName .= '_' . ( $i + 1 );
    }

    $connection = [];

    $connection['host']     = promptRequired('Please enter your database host', $green, $reset, 'Database host is required.');
    $connection['user']     = promptRequired('Please enter your database username', $green, $reset, 'Database username is required.');
    $connection['database'] = promptRequired('Please enter your database name', $green, $reset, 'Database name is required.');

    $dbDriver = promptOptional('Please enter database driver', 'mysql', $green, $yellow, $reset);
    if ( $dbDriver !== null ) {
        $connection['driver'] = $dbDriver;
    }

    while ( true ) {
        $dbPort = promptOptional('Please enter database port', '3306', $green, $yellow, $reset);

        if ( $dbPort === null ) {
            break;
        }

        if ( ctype_digit($dbPort) ) {
            $connection['port'] = (int) $dbPort;
            break;
        }

        fwrite(STDERR, "\n❌ Invalid database port. Please enter a number.\n");
    }

    $dbPassword = promptOptional('Please enter database password', "''", $green, $yellow, $reset);
    if ( $dbPassword !== null ) {
        $connection['password'] = $dbPassword;
    }

    $dbCharset = promptOptional('Please enter database charset', 'utf8mb4', $green, $yellow, $reset);
    if ( $dbCharset !== null ) {
        $connection['charset'] = $dbCharset;
    }

    $dbCollation = promptOptional('Please enter database collation', 'utf8mb4_unicode_ci', $green, $yellow, $reset);
    if ( $dbCollation !== null ) {
        $connection['collation'] = $dbCollation;
    }

    $dbPrefix = promptOptional('Please enter database prefix', "''", $green, $yellow, $reset);
    if ( $dbPrefix !== null ) {
        $connection['prefix'] = $dbPrefix;
    }

    while ( true ) {
        $dbStrict = promptOptional('Please enter database strict mode (true/false)', 'true', $green, $yellow, $reset);

        if ( $dbStrict === null ) {
            break;
        }

        $strict = strtolower($dbStrict);

        if ( in_array($strict, [ 'true', 'false', '1', '0' ], true) ) {
            $connection['strict'] = in_array($strict, [ 'true', '1' ], true);
            break;
        }

        fwrite(STDERR, "\n❌ Strict mode must be true or false.\n");
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

    /*
    |--------------------------------------------------------------------------
    | Application Configuration
    |--------------------------------------------------------------------------
    |
    | Timezone, locale and calendar settings for the application.
    |
    */

    'timezone' => %TIMEZONE%,
    'locale'   => 'fa',
    'calendar' => 'jalali',

    /*
    |--------------------------------------------------------------------------
    | Bot Configuration
    |--------------------------------------------------------------------------
    |
    | `default` only selects the outgoing default driver.
    | Incoming webhook requests are resolved automatically from their
    | dedicated webhook URL (and Telegram secret header when configured).
    |
    | `paths()->config('bot.token')` remains a virtual compatibility alias and
    | resolves to bot.drivers.<default>.token. No token is duplicated here.
    |
    */

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
%DATABASE_CONFIG%
    ],

];
EOD;

$configContent = str_replace([
    '%TIMEZONE%',
    '%BOT_DRIVER%',
    '%TELEGRAM_BOT_TOKEN%',
    '%BALE_BOT_TOKEN%',
    '%TELEGRAM_WEBHOOK_SECRET%',
    '%DATABASE_CONFIG%',
], [
    var_export($timezone, true),
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