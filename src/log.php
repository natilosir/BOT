<?php

function setupErrorHandling()
{
    $logFilePath = __DIR__.'\..\..\..\..\error.txt';
    if (file_exists($logFilePath)) {
        unlink($logFilePath);
    }

    ini_set('error_reporting', E_ALL & ~E_NOTICE);
    ini_set('display_errors', 'On');
    ini_set('log_errors', 'On');
    ini_set('error_log', $logFilePath);
}
function lg($data) {
    if (is_object($data)) {
        $dataArray = json_decode(json_encode($data), true);
        error_log(print_r($dataArray, true));
    } elseif (is_array($data)) {
        ob_start();
        var_dump($data); 
        $output = ob_get_clean(); 
        error_log($output);
    } else {
        error_log($data);
    }
}

function processLogFile()
{
    $logFilePath = __DIR__.'\..\..\..\..\error.txt';

    if (file_exists($logFilePath)) {
        $content        = file_get_contents($logFilePath);
        $lines          = explode("\n", $content);
        $processedLines = [];

        foreach ($lines as $line) {
            // استخراج و نگهداری فقط زمان از تاریخ
            $line = preg_replace_callback('/^\[(\d{2}-\w{3}-\d{4}\s)(\d{2}:\d{2}:\d{2})(\s[A-Za-z\/]+)\]\s/', function ($matches) {
                return $matches[2].' '; // فقط زمان باقی بماند
            }, $line);

            // حذف عبارت PHP Notice:
            $line = preg_replace('/PHP Notice:\s*/', '', $line);

            if (! empty(trim($line))) {
                $processedLines[] = $line;
            }
        }

        file_put_contents($logFilePath, implode("\n", $processedLines));
    } else {
        file_put_contents($logFilePath, 'no dedicated error message found');
    }
}
