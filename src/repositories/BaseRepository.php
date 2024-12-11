<?php

namespace Algom\Academia1\repositories;

use PDO;

abstract class BaseRepository {
    protected PDO $db;

    public function __construct() {
        $host = 'localhost';
        $dbname = 'academia';
        $username = 'root';
        $password = '';
        
        try {
            $this->db = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (\PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            throw new \Exception("Error de conexión a la base de datos");
        }
    }

    protected function beginTransaction(): void {
        $this->db->beginTransaction();
    }

    protected function commit(): void {
        $this->db->commit();
    }

    protected function rollback(): void {
        $this->db->rollBack();
    }
}
