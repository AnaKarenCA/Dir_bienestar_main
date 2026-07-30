<?php

class Database
{
    private PDO $db;

    public function __construct()
    {
        $config = require APPROOT . '/config/database.php';

        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";

        try {
            $this->db = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }

    public function query($sql)
    {
        return $this->db->prepare($sql);
    }

    public function getConnection()
    {
        return $this->db;
    }
}