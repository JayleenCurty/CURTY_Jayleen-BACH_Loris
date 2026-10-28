<?php
// Regroupe toutes les requêtes SQL sur la table destination.

class DestinationManager {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Nombre total de destinations
    public function compter(): int {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM destination')->fetchColumn();
    }

    // Les premières destinations par ordre alphabétique (pour l'accueil)
    public function getQuelquesDestinations(int $nombre): array {
        $requete = $this->pdo->prepare(
            'SELECT ville, pays, type_destination FROM destination ORDER BY ville LIMIT :nombre'
        );
        // LIMIT doit recevoir un entier, d'où PDO::PARAM_INT
        $requete->bindValue('nombre', $nombre, PDO::PARAM_INT);
        $requete->execute();

        return $requete->fetchAll();
    }
}
