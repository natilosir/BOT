<?php

require 'Database.php';

class DB
{
    private static $connection;

    private static $table;

    private static $query;

    private static $ORDER;

    private static $limit;

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
        self::$ORDER = " ORDER BY $column $direction";

        return $this;
    }

    public function limit($limit)
    {
        self::$limit = " LIMIT $limit";

        return $this;
    }

    public function get()
    {
        $stmt = self::$connection->prepare('SELECT * FROM '.
        self::$table.
        self::$query.
        self::$ORDER.
        self::$limit

        );
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function save($params = null)
    {
        if (is_array($params)) {
            $keys   = array_keys($params);
            $column = $keys[0];
            $id     = $params[$column];
        } else {
            $column = 'id';
            $id     = $params;
        }

        if (empty($id)) {
            $columns      = implode(', ', array_keys($this->data));
            $placeholders = implode(', ', array_fill(0, count($this->data), '?'));
            $values       = array_values($this->data);
            $sql          = 'INSERT INTO '.self::$table." ($columns) VALUES ($placeholders)";
            $stmt         = self::$connection->prepare($sql);

            return $stmt->execute($values);
        } else {
            $set = '';
            foreach ($this->data as $col => $value) {
                $set .= "$col = :$col, ";
            }
            $set = rtrim($set, ', ');
            $sql = 'UPDATE '.self::$table." SET $set WHERE $column = :id";
            echo $sql;

            $stmt = self::$connection->prepare($sql);
            foreach ($this->data as $col => $value) {
                $stmt->bindValue(":$col", $value);
            }

            $stmt->bindValue(':id', $id);

            return $stmt->execute();
        }
    }

    public function count()
    {
        $stmt = self::$connection->prepare('SELECT COUNT(*) FROM '.
        self::$table.
        self::$query.
        self::$ORDER.
        self::$limit);
        $stmt->execute();

        return $stmt->fetchColumn();
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

    public function update($params, $data)
    {
        if (is_array($params)) {
            $keys   = array_keys($params);
            $column = $keys[0];
            $id     = $params[$column];
        } else {
            $column = 'id';
            $id     = $params;
        }

        $set = '';
        foreach ($data as $columnName => $value) {
            $set .= "$columnName = :$columnName, ";
        }
        $set  = rtrim($set, ', ');
        $stmt = self::$connection->prepare('UPDATE '.self::$table." SET $set WHERE $column = :id");
        foreach ($data as $columnName => $value) {
            $stmt->bindValue(":$columnName", $value);
        }
        $stmt->bindValue(':id', $id);

        return $stmt->execute();
    }

    public function delete($params)
    {
        if (is_array($params)) {
            $keys   = array_keys($params);
            $column = $keys[0];
            $id     = $params[$column];
        } else {
            $column = 'id';
            $id     = $params;
        }

        $stmt = self::$connection->prepare('DELETE FROM '.self::$table.' WHERE '.$column.' = :id');
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
