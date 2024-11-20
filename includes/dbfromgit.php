<?php

class Database
{
    private $host = 'localhost'; // Database host (typically localhost in Laragon)

    private $db_name = 'telegram_bot'; // Your database name

    private $username = 'root'; // Your database username

    private $password = ''; // Your database password

    private $connection;

    public function getConnection()
    {
        $this->connection = null;

        try {
            $this->connection = new PDO("mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4", $this->username, $this->password);

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo 'Connection error: '.$exception->getMessage();
        }

        return $this->connection;
    }
}
