<?php
// Connexion à la base de données à partir du fichier de configuration.

class Database {
    const DATABASE_CONFIGURATION_FILE = __DIR__ . '/../../config/database.ini';

    private PDO $pdo;

    public function __construct() {
        $config = parse_ini_file(self::DATABASE_CONFIGURATION_FILE);

        if ($config === false) {
            throw new Exception('Fichier de configuration introuvable : copiez config/database.example.ini en config/database.ini');
        }

        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";

        $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function getPdo(): PDO {
        return $this->pdo;
    }
}
