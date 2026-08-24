<?php

class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {

            $host = '127.0.0.1';
            $port = '3306';
            $database = 'loan_management_db';
            $username = 'root';
            $password = '';

            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

            try {

                self::$instance = new PDO(
                    $dsn,
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                        PDO::ATTR_DEFAULT_FETCH_MODE =>
                            PDO::FETCH_ASSOC,

                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );

            } catch (PDOException $e) {

                die(
                    'Database connection failed: ' .
                    $e->getMessage()
                );
            }
        }

        return self::$instance;
    }
}