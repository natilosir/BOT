<?php

class Database
{
    private $host = 'localhost'; // Database host

    private $db_name = 'natilos'; // Database name

    private $username = 'root'; // Database username

    private $password = ''; // Database password

    private $connection;

    public function getConnection()
    {
        $this->connection = null;

        try {
            $this->connection = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo 'Connection error: '.$exception->getMessage();
        }

        return $this->connection;
    }
}
