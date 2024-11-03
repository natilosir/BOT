<?php

// Configuration
$botToken = 'YOUR_BOT_TOKEN';
$apiUrl = "https://api.telegram.org/bot$botToken/";

// Database connection
$mysqli = new mysqli('localhost', 'username', 'password', 'telegram_bot');

// Check for connection errors
if ($mysqli->connect_error) {
    exit('Connect Error ('.$mysqli->connect_errno.') '.$mysqli->connect_error);
}

// Get the webhook updates
$content = file_get_contents('php://input');
$update = json_decode($content, true);

if (! $update) {
    exit;
}

$message = $update['message'];
$chatId = $message['chat']['id'];
$text = $message['text'];

if ($text == '/start') {
    $firstName = $message['chat']['first_name'];
    $lastName = $message['chat']['last_name'];
    $telegramId = $message['chat']['id'];
    $phoneNumber = isset($message['contact']['phone_number']) ? $message['contact']['phone_number'] : '';

    // Insert user data into the database
    $stmt = $mysqli->prepare('INSERT INTO users (telegram_id, first_name, last_name, phone_number) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('isss', $telegramId, $firstName, $lastName, $phoneNumber);
    $stmt->execute();
    $stmt->close();

    $keyboard = [
        'keyboard' => [
            [['text' => 'Opt1'], ['text' => 'Opt2']],
            [['text' => 'Opt3'], ['text' => 'Opt4']],
            [['text' => 'Opt5'], ['text' => 'Opt6']],
        ],
        'resize_keyboard' => true,
        'one_time_keyboard' => true,
    ];

    $response = [
        'chat_id' => $chatId,
        'text' => "Thank you for registering, $firstName!",
        'reply_markup' => json_encode($keyboard),
    ];

    file_get_contents($apiUrl.'sendMessage?'.http_build_query($response));
} else {
    switch ($text) {
        case 'Opt1':
            include 'path_to_file1.php';
            break;
        case 'Opt2':
            include 'path_to_file2.php';
            break;
        case 'Opt3':
            include 'path_to_file3.php';
            break;
        case 'Opt4':
            include 'path_to_file4.php';
            break;
        case 'Opt5':
            include 'path_to_file5.php';
            break;
        case 'Opt6':
            include 'path_to_file6.php';
            break;
        default:
            file_get_contents($apiUrl."sendMessage?chat_id=$chatId&text=Invalid option selected.");
            break;
    }
}
