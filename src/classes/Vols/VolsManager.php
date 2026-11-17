<?php

namespace Vols;

use ComptableInterface;
use Database;
use DateTime;
use PDO;

// Regroupe toutes les requêtes SQL sur la table vol.
class VolsManager implements ComptableInterface {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Nombre total de vols
    public function compter(): int {
        return (int) $this->database->getPdo()->query('SELECT COUNT(*) FROM vol')->fetchColumn();
    }

    // Un vol par son id, ou null s'il n'existe pas
    public function getVol(int $id): ?Vol {
        $stmt = $this->database->getPdo()->prepare('SELECT * FROM vol WHERE id = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $ligne = $stmt->fetch();

        return $ligne === false ? null : $this->creerVol($ligne);
    }

    // Transforme une ligne de la base en objet Vol
    private function creerVol(array $ligne): Vol {
        $vol = new Vol();
        $vol->setId((int) $ligne['id']);
        $vol->setCompagnie($ligne['compagnie']);
        $vol->setDateAller(new DateTime($ligne['date_aller']));
        $vol->setDateRetour(new DateTime($ligne['date_retour']));
        $vol->setDureeVol((int) $ligne['duree_vol']);
        $vol->setPrixPersonne((float) $ligne['prix_personne']);
        $vol->setPlacesDisponibles((int) $ligne['places_disponibles']);
        $vol->setIdDestination((int) $ligne['id_destination']);

        return $vol;
    }
}
