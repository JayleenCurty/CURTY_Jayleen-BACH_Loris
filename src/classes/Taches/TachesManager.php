<?php

namespace Taches;

use Database;

// Regroupe toutes les requêtes SQL sur la table taches (checklist avant le départ).
// Une tâche n'a pas de colonne id_utilisateur : pour vérifier qu'elle appartient
// à la personne connectée, on passe par la table voyage (JOIN).
class TachesManager {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Toutes les tâches d'un voyage de cet utilisateur, les tâches à faire en premier
    public function getTachesVoyage(int $idVoyage, int $idUtilisateur): array {
        $sql = 'SELECT t.*
                FROM taches t
                JOIN voyage v ON v.id = t.id_voyage
                WHERE t.id_voyage = :id_voyage AND v.id_utilisateur = :id_utilisateur
                ORDER BY t.faite, t.id';

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_voyage', $idVoyage);
        $stmt->bindValue(':id_utilisateur', $idUtilisateur);
        $stmt->execute();

        return array_map(fn(array $ligne) => $this->creerTache($ligne), $stmt->fetchAll());
    }

    // Ajoute une tâche (la page doit avoir vérifié que le voyage appartient à l'utilisateur)
    public function addTache(Tache $tache): int {
        $sql = 'INSERT INTO taches (libelle, faite, id_voyage) VALUES (:libelle, :faite, :id_voyage)';

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':libelle', $tache->getLibelle());
        $stmt->bindValue(':faite', $tache->isFaite() ? 1 : 0);
        $stmt->bindValue(':id_voyage', $tache->getIdVoyage());
        $stmt->execute();

        return (int) $this->database->getPdo()->lastInsertId();
    }

    // Coche ou décoche une tâche (inverse "faite"), seulement si elle est à cet utilisateur
    public function basculerTache(int $idTache, int $idUtilisateur): bool {
        $sql = 'UPDATE taches t
                JOIN voyage v ON v.id = t.id_voyage
                SET t.faite = NOT t.faite
                WHERE t.id = :id AND v.id_utilisateur = :id_utilisateur';

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $idTache);
        $stmt->bindValue(':id_utilisateur', $idUtilisateur);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Supprime une tâche, seulement si elle appartient à un voyage de cet utilisateur
    public function removeTache(int $idTache, int $idUtilisateur): bool {
        $sql = 'DELETE t FROM taches t
                JOIN voyage v ON v.id = t.id_voyage
                WHERE t.id = :id AND v.id_utilisateur = :id_utilisateur';

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $idTache);
        $stmt->bindValue(':id_utilisateur', $idUtilisateur);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Transforme une ligne de la base en objet Tache
    private function creerTache(array $ligne): Tache {
        $tache = new Tache();
        $tache->setId((int) $ligne['id']);
        $tache->setLibelle($ligne['libelle']);
        $tache->setFaite((bool) $ligne['faite']);
        $tache->setIdVoyage((int) $ligne['id_voyage']);

        return $tache;
    }
}
