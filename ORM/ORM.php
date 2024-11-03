<?php

require 'Database.php';

class DB
{
    private static $connection;

    private static $table;

    private static $query;

    private $data = [];

    public function __construct()
    {
        $database         = new Database();
        self::$connection = $database->getConnection();
    }

    public static function Table($table)
    {
        self::$table = $table;
        self::$query = '';

        return new self();
    }

    public function where($column, $value)
    {
        self::$query .= " WHERE $column = '$value'";

        return $this;
    }

    public function orwhere($column, $value)
    {
        self::$query .= " OR $column = '$value'";

        return $this;
    }

    public function andwhere($column, $value)
    {
        self::$query .= " AND $column = '$value'";

        return $this;
    }

    public function orderBy($column, $direction)
    {
        self::$query .= " ORDER BY $column $direction";

        return $this;
    }

    public function limit($limit)
    {
        self::$query .= " LIMIT $limit";

        return $this;
    }

    public function get()
    {
        $stmt = self::$connection->prepare('SELECT * FROM '.self::$table.self::$query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function save($id = null)
    {
        if (empty($id)) {
            $columns      = implode(', ', array_keys($this->data));
            $placeholders = implode(', ', array_fill(0, count($this->data), '?'));
            $values       = array_values($this->data);
            $sql          = 'INSERT INTO '.self::$table." ($columns) VALUES ($placeholders)";
            $stmt         = self::$connection->prepare($sql);
            $stmt->execute($values);
        }if (is_int($id)) {
            $set = '';
            foreach ($this->data as $column => $value) {
                $set .= "$column = :$column, ";
            }
            $set  = rtrim($set, ', ');
            $sql  = 'UPDATE '.self::$table." SET $set WHERE id = :id";
            $stmt = self::$connection->prepare($sql);
            foreach ($this->data as $column => $value) {
                $stmt->bindValue(":$column", $value);
            }
            $stmt->bindValue(':id', $id);

            return $stmt->execute();
        }
    }

    public function count()
    {
        $stmt = self::$connection->prepare('SELECT COUNT(*) FROM '.self::$table.self::$query);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function search($conditions)
    {
        $query = ' WHERE ';
        $first = true;
        foreach ($conditions as $column => $value) {
            if (! $first) {
                $query .= ' AND ';
            }
            $query .= "$column LIKE '%$value%'";
            $first = false;
        }
        self::$query .= $query;

        return $this;
    }

    public function insert($data)
    {
        if (is_array($data)) {
            $columns      = implode(', ', array_keys($data));
            $placeholders = ':'.implode(', :', array_keys($data));
            $stmt         = self::$connection->prepare('INSERT INTO '.self::$table." ($columns) VALUES ($placeholders)");
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            return $stmt->execute();
        } else {
            $stmt = self::$connection->prepare('INSERT INTO '.self::$table.' (name) VALUES (:name)');
            $stmt->bindValue(':name', $data);

            return $stmt->execute();
        }
    }

    public function update($id, $data)
    {
        $set = '';
        foreach ($data as $column => $value) {
            $set .= "$column = :$column, ";
        }
        $set  = rtrim($set, ', ');
        $stmt = self::$connection->prepare('UPDATE '.self::$table." SET $set WHERE id = :id");
        foreach ($data as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = self::$connection->prepare('DELETE FROM '.self::$table.' WHERE id = :id');
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function query($sql)
    {
        $stmt = self::$connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
