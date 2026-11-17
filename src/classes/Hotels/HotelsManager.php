<?php

namespace Hotels;

use ComptableInterface;
use Database;
use PDO;

// Regroupe toutes les requêtes SQL sur la table hotel.
class HotelsManager implements ComptableInterface {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Nombre total d'hôtels
    public function compter(): int {
        return (int) $this->database->getPdo()->query('SELECT COUNT(*) FROM hotel')->fetchColumn();
    }

    // Un hôtel par son id, ou null s'il n'existe pas
    public function getHotel(int $id): ?Hotel {
        $stmt = $this->database->getPdo()->prepare('SELECT * FROM hotel WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $ligne = $stmt->fetch();

        return $ligne === false ? null : $this->creerHotel($ligne);
    }

    // Transforme une ligne de la base en objet Hotel
    private function creerHotel(array $ligne): Hotel {
        $hotel = new Hotel();
        $hotel->setId((int) $ligne['id']);
        $hotel->setNom($ligne['nom']);
        $hotel->setEtoiles($ligne['etoiles'] !== null ? (int) $ligne['etoiles'] : null);
        $hotel->setPrixNuit((float) $ligne['prix_nuit']);
        $hotel->setCapacite((int) $ligne['capacite']);
        $hotel->setIdDestination((int) $ligne['id_destination']);

        return $hotel;
    }
}
