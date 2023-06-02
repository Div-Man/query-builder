<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

$configMySQL = [
    'dbtype' => 'mysql',
    'host' => 'localhost',
    'port' => '3306',
    'username' => 'dimak',
    'password' => '123456',
    'dbname' => 'testbase',
];

$configPostgreSQL = [
    'dbtype' => 'pgsql',
    'host' => 'localhost',
    'port' => '5432',
    'username' => 'dimak',
    'password' => '123456',
    'dbname' => 'mybase',
];

class QueryBuilder {

    private object $pdo;
    private ?string $table = null;
    private ?string $select = null;
    private ?string $where = null;
    private ?string $whereValue = null;
    private ?string $orderBy = null;
    private ?string $groupBy = null;
    private ?string $limit = null;
    private ?string $find = null;
    private ?string $findId = null;
    private ?string $leftJoin = null;
    private ?string $join = null;
    private ?string $count = null;

    public function __construct($dbOptions) {
        $dsn = $dbOptions['dbtype'] . ":host=" . $dbOptions['host'] . ";port=" . $dbOptions['port'] . ";dbname=" . $dbOptions['dbname'] . ";";
        try {
            $this->pdo = new \PDO($dsn, $dbOptions['username'], $dbOptions['password']);
        } catch (Exception $e) {
            echo 'Ошибка подключения к базе.';
        }
    }

    public function table($table): QueryBuilder {
        $this->table = $table;
        return $this;
    }

    public function select($columns): QueryBuilder {
        $this->select = implode(", ", $columns);
        return $this;
    }

    public function get(): array {
        $valueColumns = '';
        $valueColumns = !empty($this->select) ? $this->select : "*";

        $query = 'SELECT ' . $valueColumns . ' FROM ' . $this->table .
                $this->leftJoin .
                $this->join .
                $this->find .
                $this->where .
                $this->orderBy .
                $this->limit .
                $this->groupBy;

        $sth = $this->pdo->prepare($query);

        if (!empty($this->findId))
            $sth->bindParam(':id', $this->findId, PDO::PARAM_INT);
        if (!empty($this->whereValue))
            $sth->bindParam(':value', $this->whereValue, PDO::PARAM_STR);

        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public function where($column, $condition, $value): QueryBuilder {
        $this->where .= ' WHERE ' . $column . ' ' . $condition . ' :value';
        $this->whereValue = $value;
        return $this;
    }

    public function orderBy($column, $sort = 'ASC'): QueryBuilder {
        $this->orderBy = ' ORDER BY ' . $column . ' ' . $sort;
        return $this;
    }

    public function groupBy($column): array {
        $this->groupBy = ' GROUP BY ' . $column;
        return $this->get();
    }

    public function limit($value): QueryBuilder {
        $this->limit .= ' LIMIT ' . $value;
        return $this;
    }

    public function find($id): array {
        $this->find = ' WHERE id = :id';
        $this->findId = $id;
        return $this->get();
    }

    public function leftJoin($tableJoin, $table_id1, $table_id2): QueryBuilder {
        $this->leftJoin = ' LEFT JOIN ' . $tableJoin . ' ON ' . $table_id1 . ' = ' . $table_id2;
        return $this;
    }

    public function join($tableJoin, $table_id1, $table_id2): QueryBuilder {
        $this->join = ' INNER JOIN ' . $tableJoin . ' ON ' . $table_id1 . ' = ' . $table_id2;
        return $this;
    }

    public function count(): array {
        $this->select = ' count(*) AS count ';
        return $this->get();
    }

    public function insert($data): bool {
        //Массовая вставка
        if (array_key_exists(0, $data)) {
            return $this->insertMultiple($data);
        }
        //Одиночная вставка
        else {
            return $this->insertSingle($data);
        }
    }

    public function insertSingle($data): bool {
        $query = 'INSERT INTO ' . $this->table . ' ( ';
        $columns = '';
        $values = '';
        $str = '';
        $values .= '(';
        foreach ($data as $key => $value) {
            $columns .= $key . ', ';
            $values .= '?, ';
        }
        $values = substr_replace($values, '', -2, 1);
        $values .= ');';
        $values = substr_replace($values, ';', -1, 1);

        $columns = substr_replace($columns, ')', -2, 1);

        $query .= $columns . ' VALUES ' . $values;

        $sth = $this->pdo->prepare($query);

        $i = 1;
        foreach ($data as $key => $value) {
            $sth->bindParam($i, $data[$key]);
            $i++;
        }
        return $sth->execute();
    }

    public function insertMultiple($data): bool {
        $query = 'INSERT INTO ' . $this->table . ' ( ';
        $columns = '';
        $values = '';
        $str = '';

        foreach ($data as $array => $subArray) {
            $values .= '(';
            foreach ($subArray as $key => $value) {
                $str .= $key . ', ';
                $columns = $str;
                $values .= '?, ';
            }
            $values = substr_replace($values, '', -2, 2);
            $values .= '), ';
            $str = '';
        }
        $values = substr_replace($values, ';', -2, 1);
        $columns = substr_replace($columns, ')', -2, 1);
        $query .= $columns . 'VALUES ' . $values;
        $sth = $this->pdo->prepare($query);

        $i = 1;

        foreach ($data as $array => $subArray) {
            foreach ($subArray as $key => $value) {
                $sth->bindParam($i, $subArray[$key]);
                $i++;
            }
        }
        return $sth->execute();
    }

    public function update($data): bool {
        $query = 'UPDATE ' . $this->table . ' SET ';
        $columns = "";
        $values = [];

        foreach ($data as $key => $value) {
            $columns .= $key . ' = :' . $key . ', ';
            $values[] = $value;
        }
        $columns = substr_replace($columns, '', -2, 1);
        $query .= $columns . $this->where . ';';

        $sth = $this->pdo->prepare($query);

        $data['value'] = $this->whereValue;

        foreach ($data as $key => $value) {
            $sth->bindParam(':' . $key, $data[$key]);
        }

        return $sth->execute();
    }

    public function delete(): bool {
        $query = 'DELETE FROM ' . $this->table . ' ' . $this->where . ';';
        $sth = $this->pdo->prepare($query);
        return $sth->execute([':value' => $this->whereValue]);
    }

}

//$db = new QueryBuilder($configPostgreSQL);
$db = new QueryBuilder($configMySQL);


