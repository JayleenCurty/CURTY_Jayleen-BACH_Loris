<?php
// Regroupe toutes les requêtes SQL sur la table hotel.

class HotelManager {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Nombre total de lignes dans la table hotel
    public function compter(): int {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM hotel')->fetchColumn();
    }
}
