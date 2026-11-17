<?php

namespace Destinations;

use ComptableInterface;
use Database;
use PDO;

// Regroupe toutes les requêtes SQL sur la table destination.
class DestinationsManager implements ComptableInterface {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Nombre total de destinations
    public function compter(): int {
        return (int) $this->database->getPdo()->query('SELECT COUNT(*) FROM destination')->fetchColumn();
    }

    // Les premières destinations par ordre alphabétique (tableau d'objets Destination)
    public function getDestinations(int $nombre): array {
        $sql = 'SELECT * FROM destination ORDER BY ville LIMIT :nombre';

        $stmt = $this->database->getPdo()->prepare($sql);
        // LIMIT doit recevoir un entier, d'où PDO::PARAM_INT
        $stmt->bindValue(':nombre', $nombre, PDO::PARAM_INT);
        $stmt->execute();

        // Transformation des tableaux associatifs en objets Destination
        return array_map(fn(array $ligne) => $this->creerDestination($ligne), $stmt->fetchAll());
    }

    // Une destination par son id, ou null si elle n'existe pas
    public function getDestination(int $id): ?Destination {
        $stmt = $this->database->getPdo()->prepare('SELECT * FROM destination WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $ligne = $stmt->fetch();

        return $ligne === false ? null : $this->creerDestination($ligne);
    }

    // Transforme une ligne de la base en objet Destination
    private function creerDestination(array $ligne): Destination {
        $destination = new Destination();
        $destination->setId((int) $ligne['id']);
        $destination->setPays($ligne['pays']);
        $destination->setPaysEn($ligne['pays_en'] ?? null);
        $destination->setVille($ligne['ville']);
        $destination->setVilleEn($ligne['ville_en'] ?? null);
        $destination->setCodeAeroport($ligne['code_aeroport'] ?? '');
        $destination->setDescription($ligne['description'] ?? null);
        $destination->setDescriptionEn($ligne['description_en'] ?? null);
        $destination->setImage($ligne['image'] ?? null);
        $destination->setTypeDestination($ligne['type_destination']);

        return $destination;
    }
}
