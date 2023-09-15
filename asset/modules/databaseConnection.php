<?php
date_default_timezone_set("Africa/Lagos");

class Db {
    private $host = 'localhost';
    private $dbname = 'foodie';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    private $options = [
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    private $pdo;

    public function connect() {
        if (!$this->pdo) {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $this->pdo = new PDO($dsn, $this->username, $this->password, $this->options);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
        return $this->pdo;
    }

    public function disconnect() {
        $this->pdo = null;
    }
}
?>
