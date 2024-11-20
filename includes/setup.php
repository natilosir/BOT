<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$host     = 'localhost';
$username = 'root';  // Default for Laragon
$password = '';      // Default for Laragon
$database = 'telegram_bot';  // Your bot's database name

// Connect to MySQL using PDO
try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to MySQL successfully.\n";

    // Create the database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '$database' created successfully or already exists.\n";

    // Select the database
    $pdo->exec("USE $database");

    // Create `users` table
    $createUsersTable = '
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        telegram_id BIGINT NOT NULL UNIQUE,
        first_name VARCHAR(255),
        last_name VARCHAR(255),
        phone_number VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )';
    $pdo->exec($createUsersTable);
    echo "Table 'users' created successfully.\n";

    // Create `messages` table
    $createMessagesTable = '
    CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id BIGINT NOT NULL,
        receiver_id BIGINT NOT NULL,
        content TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )';
    $pdo->exec($createMessagesTable);
    echo "Table 'messages' created successfully.\n";

    // Create `blocked_users` table
    $createBlockedUsersTable = '
    CREATE TABLE IF NOT EXISTS blocked_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT NOT NULL,
        blocked_user_id BIGINT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )';
    $pdo->exec($createBlockedUsersTable);
    echo "Table 'blocked_users' created successfully.\n";
} catch (PDOException $e) {
    exit('Database connection failed: '.$e->getMessage());
}

// Close connection
$pdo = null;

echo "Database setup complete.\n";
