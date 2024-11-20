<?php

require 'Database.php';

class DB
{
    private static $connection;

    private static $table;

    private static $query;

    private static $ORDER;

    private static $limit;

    private static $distinct = false;

    private static $columns = '*';

    private $data = [];

    public function __construct()
    {
        $database         = new Database();
        self::$connection = $database->getConnection();
    }

    public static function Table($table)
    {
        self::$table    = $table;
        self::$query    = '';
        self::$ORDER    = '';
        self::$limit    = '';
        self::$distinct = false;
        self::$columns  = '*';

        return new self();
    }

    public function distinct()
    {
        self::$distinct = true;

        return $this;
    }

    public static function select($columns)
    {
        if (is_array($columns)) {
            self::$columns = implode(', ', $columns);
        } else {
            self::$columns = $columns;
        }

        return new self();
    }

    public static function search($conditions)
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

        return new self();
    }

    public static function where($column, $value = null, $operator = '=', $type = 'AND')
    {
        if (is_array($column)) {
            foreach ($column as $col => $val) {
                if (empty(self::$query)) {
                    self::$query = " WHERE $col $operator '$val'";
                } else {
                    self::$query .= " $type $col $operator '$val'";
                }
            }
        } elseif ($value === null) {
            if (empty(self::$query)) {
                self::$query = " WHERE $column";
            } else {
                self::$query .= " $type $column";
            }
        } else {
            if (empty(self::$query)) {
                self::$query = " WHERE $column $operator '$value'";
            } else {
                self::$query .= " $type $column $operator '$value'";
            }
        }

        return new self();
    }

    public static function orderBy($column, $direction)
    {
        self::$ORDER = " ORDER BY $column $direction";

        return new self();
    }

    public static function limit($limit)
    {
        self::$limit = " LIMIT $limit";

        return new self();
    }

    public static function first()
    {
        self::$limit = ' LIMIT 1';

        return self::get();
    }

    public static function get()
    {
        $selectPart = self::$distinct ? 'SELECT DISTINCT '.self::$columns : 'SELECT '.self::$columns;
        $sql        = $selectPart.
            ' FROM '.self::$table.
            self::$query.
            self::$ORDER.
            self::$limit;
        // echo $sql;
        $stmt = self::$connection->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        if (count($result) === 1) {
            return $result[0];
        }
    
        return $result; 
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

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
