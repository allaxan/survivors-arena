<?php
/**
 * Configuration de la connexion à la base de données
 */

class Database {
    private static $instance = null;
    private $pdo;
    
    // Configuration BDD
    private $host = 'localhost';
    private $dbname = 'perso_db';
    private $username = 'root';
    private $password = '';
    
    private function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }
    
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection(): PDO {
        return $this->pdo;
    }
}
