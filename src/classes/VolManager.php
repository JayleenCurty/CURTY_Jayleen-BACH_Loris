<?php
// Regroupe toutes les requêtes SQL sur la table vol.

class VolManager {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Nombre total de lignes dans la table vol
    public function compter(): int {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM vol')->fetchColumn();
    }
}
