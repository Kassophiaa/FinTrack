<?php

class myDB {

    public $res;
    private $conn;
    private $host;
    private $username;
    private $password;
    private $dbname;

    public function __construct() {
        // Credentials live in db_config.php (not committed). Copy db_config.example.php to create it.
        $config = require __DIR__ . '/db_config.php';
        $this->host = $config['host'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->dbname = $config['dbname'];

        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);

        if ($this->conn->connect_error) {
            throw new Exception("Database connection failed: " . $this->conn->connect_error);
        }
    }

    public function __destruct() {
        $this->conn->close();
    }

    public function insert($sql, $params = [], $types = "") {
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        if (!empty($params) && !empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        $result = $stmt->execute();
        $this->res = $result;
        $stmt->close();

        return $result;
    }

    public function select($sql, $params = [], $types = "") {
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        if (!empty($params) && !empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $this->res = $stmt->get_result();
        $stmt->close();

        return $this->res;
    }

    public function update($sql, $params = [], $types = "") {
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        if (!empty($params) && !empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        $result = $stmt->execute();
        $this->res = $result;
        $stmt->close();

        return $result;
    }

    public function delete($sql, $params = [], $types = "") {
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        if (!empty($params) && !empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        $result = $stmt->execute();
        $this->res = $result;
        $stmt->close();

        return $result;
    }

    public function search($sql, $params = [], $types = "") {
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return false;
        }

        if (!empty($params) && !empty($types)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $this->res = $stmt->get_result();
        $stmt->close();

        return $this->res;
    }

    public function getInsertID() {
        return $this->conn->insert_id;
    }

    public function numRows() {
        if ($this->res) {
            return $this->res->num_rows;
        }
        return 0;
    }

}

?>