<?php

class DatabaseSingleton
{
    private ?PDO $pdo = null;

    // Private constructor — prevents direct instantiation with 'new'
    private function __construct()
    {
        $dsn = "mysql:host=127.0.0.1;port=3306;dbname=demo;charset=utf8mb4";
        $this->pdo = new PDO($dsn, "http-and-session-user", "http-and-session-pass", [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    // Public static method — the only way to get the instance
    public static function instance(): static
    {
        static $instance;

        if (is_null($instance)) {
            $instance = new static(); // Created only on first call
        }

        return $instance; // All subsequent calls return the same object
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}