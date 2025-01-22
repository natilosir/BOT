<?php

$sourceDir = __DIR__ . '/../telegram-bot-sdk/';
$destinationDir = __DIR__ . '/../../../';

if (is_dir($sourceDir)) {
    $files = scandir($sourceDir);
    
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $sourceFile = $sourceDir . $file;
            $destinationFile = $destinationDir . $file;

            if (is_dir($sourceFile)) {
                rename($sourceFile, $destinationFile);
            } else {
                rename($sourceFile, $destinationFile);
            }
        }
    }

    rmdir($sourceDir);
    echo "The application is ready to run. Please read the documentation.";
} else {
    echo "ERROR";
}
?>
