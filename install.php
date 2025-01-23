<?php

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
if (! is_dir($sourceDir)) {
    function prompt($message)
    {
        echo $message.': ';

        return trim(fgets(STDIN));
    }

    // ANSI color codes
    $yellow = "\033[33m"; // Yellow color
    $green  = "\033[32m";  // Green color
    $reset  = "\033[0m";   // Reset color to default

    // Get user input with different colors
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

    file_put_contents('config.php', $configContent);

    echo 'The application is ready to run. Please read the documentation.';
} else {
    echo 'ERROR';
}
