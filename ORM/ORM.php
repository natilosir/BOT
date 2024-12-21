<?php

require 'Database.php';

class DB
{
    private static $connection;

    private static $table;

    private static $query;

    private static $ORDER;

    private static $limit;

    private static $json;

    private static $distinct = false;

    private static $columns = '*';

    private static $params = [];

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
        self::$params   = [];

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
        if (empty($conditions)) {
            return new self();
        }

        foreach ($conditions as $column => $value) {
            if (empty(self::$query)) {
                self::$query = " WHERE $column LIKE :$column";
            } else {
                self::$query .= " AND $column LIKE :$column";
            }
            self::$params[":$column"] = "%$value%";
        }

        return new self();
    }

    public static function where($column, $operator = null, $value = null, $type = 'AND')
    {
        if (is_array($column)) {
            foreach ($column as $col => $val) {
                if (empty(self::$query)) {
                    self::$query = " WHERE $col = :$col";
                } else {
                    self::$query .= " $type $col = :$col";
                }
                self::$params[":$col"] = $val;
            }

            return new self();
        }

        if (func_num_args() === 2) {
            $value    = $operator;
            $operator = '=';
        }

        $condition = "$column $operator :$column";

        if (empty(self::$query)) {
            self::$query = " WHERE $condition";
        } else {
            self::$query .= " $type $condition";
        }

        self::$params[":$column"] = $value;

        return new self();
    }

    public static function orderBy($column, $direction)
    {
        $direction = strtoupper($direction);

        if ($direction === 'MIN') {
            $direction = 'ASC';
        } elseif ($direction === 'MAX') {
            $direction = 'DESC';
        }

        if (! in_array($direction, ['ASC', 'DESC'])) {
            throw new InvalidArgumentException("Invalid order direction: $direction");
        }

        self::$ORDER = " ORDER BY $column $direction";

        return new self();
    }

    public static function limit($limit)
    {
        self::$limit = " LIMIT $limit";

        return new self();
    }

    public static function json()
    {
        self::$json = '1';

        return new self();
    }

    public static function first()
    {
        self::$limit = ' LIMIT 1';
        $result      = self::get();

        if (self::$json) {
            return json_encode($result);
        }

        return $result;
    }

    public static function get()
    {
        $selectPart = self::$distinct ? 'SELECT DISTINCT '.self::$columns : 'SELECT '.self::$columns;
        $sql        = $selectPart.
            ' FROM '.self::$table.
            self::$query.
            self::$ORDER.
            self::$limit;

        $stmt = self::$connection->prepare($sql);

        foreach (self::$params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        if (count($result) === 1) {
            return $result[0];
        }

        if (self::$json) {
            $result = json_encode($result);
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
        $sql = 'SELECT COUNT(*) FROM '.self::$table.self::$query.self::$ORDER.self::$limit;

        $stmt = self::$connection->prepare($sql);

        foreach (self::$params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

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

    public function update($params, $data = null)
    {
        if (empty($data)) {
            $data   = $params;
            $params = null;
        }

        if (empty(self::$query)) {
            if (is_array($params)) {
                foreach ($params as $column => $value) {
                    self::$query .= empty(self::$query) ? " WHERE $column = :w_$column" : " AND $column = :w_$column";
                    self::$params[":w_$column"] = $value;
                }
            } elseif ($params !== null) {
                self::$query           = ' WHERE id = :w_id';
                self::$params[':w_id'] = $params;
            } else {
                throw new Exception('Missing WHERE clause for update.');
            }
        }

        $set = '';
        foreach ($data as $column => $value) {
            $set .= "$column = :s_$column, ";
            self::$params[":s_$column"] = $value;
        }
        $set = rtrim($set, ', ');

        $query = 'UPDATE '.self::$table.' SET '.$set.self::$query;

        $stmt = self::$connection->prepare($query);

        foreach (self::$params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        return $stmt->execute();
    }

    public function delete($params = null)
    {
        if (empty(self::$query)) {
            if (is_int($params)) {
                self::$query         = ' WHERE id = :id';
                self::$params[':id'] = $params;
            } elseif (is_array($params)) {
                foreach ($params as $column => $value) {
                    self::$query .= empty(self::$query) ? " WHERE $column = :w_$column" : " AND $column = :w_$column";
                    self::$params[":w_$column"] = $value;
                }
            } elseif ($params === null) {
                throw new Exception('Missing WHERE clause for delete.');
            }
        }
        $sql  = 'DELETE FROM '.self::$table.self::$query;
        $stmt = self::$connection->prepare($sql);

        foreach (self::$params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        return $stmt->execute();
    }

    public function query($sql)
    {
        $stmt = self::$connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    private static function buildSQL()
    {
        $selectPart = self::$distinct ? 'SELECT DISTINCT '.self::$columns : 'SELECT '.self::$columns;

        return $selectPart.' FROM '.self::$table.self::$query.self::$ORDER.self::$limit;
    }
}
