<?php

namespace Voyages;

use Database;
use DateTime;

// Regroupe toutes les requêtes SQL sur la table voyage.
class VoyagesManager implements VoyagesManagerInterface {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Tous les voyages d'un utilisateur, du départ le plus proche au plus lointain
    public function getVoyagesUtilisateur(int $idUtilisateur): array {
        $sql = 'SELECT * FROM voyage WHERE id_utilisateur = :id_utilisateur ORDER BY date_aller';

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id_utilisateur', $idUtilisateur);
        $stmt->execute();

        // Transformation des tableaux associatifs en objets Voyage
        return array_map(fn(array $ligne) => $this->creerVoyage($ligne), $stmt->fetchAll());
    }

    // Un voyage, seulement s'il appartient à cet utilisateur (sinon null).
    // On filtre sur les deux id : changer le numéro dans l'URL ne donne pas accès au voyage d'un autre.
    public function getVoyageUtilisateur(int $idVoyage, int $idUtilisateur): ?Voyage {
        $sql = 'SELECT * FROM voyage WHERE id = :id AND id_utilisateur = :id_utilisateur';

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':id', $idVoyage);
        $stmt->bindValue(':id_utilisateur', $idUtilisateur);
        $stmt->execute();

        $ligne = $stmt->fetch();

        return $ligne === false ? null : $this->creerVoyage($ligne);
    }

    // Enregistre un nouveau voyage et retourne son id
    public function addVoyage(Voyage $voyage): int {
        $sql = 'INSERT INTO voyage
                    (nom, type_voyage, nbr_personne, budget_personne,
                     date_aller, date_retour, type_destination, id_utilisateur)
                VALUES
                    (:nom, :type_voyage, :nbr_personne, :budget_personne,
                     :date_aller, :date_retour, :type_destination, :id_utilisateur)';

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierCriteres($stmt, $voyage);
        $stmt->bindValue(':id_utilisateur', $voyage->getIdUtilisateur());
        $stmt->execute();

        return (int) $this->database->getPdo()->lastInsertId();
    }

    // Enregistre les critères modifiés d'un voyage.
    // Si un critère qui compte pour le vol ou l'hôtel change (dates, nombre de personnes,
    // budget, type de destination), le choix déjà fait n'est peut-être plus valable :
    // on l'annule et le voyage repasse "en préparation".
    // Retourne true si le choix du vol / de l'hôtel a été annulé.
    public function updateVoyage(Voyage $voyage): bool {
        $ancien = $this->getVoyageUtilisateur($voyage->getId(), $voyage->getIdUtilisateur());
        if ($ancien === null) {
            return false;
        }

        $criteresModifies =
            $ancien->getDateAller() != $voyage->getDateAller()
            || $ancien->getDateRetour() != $voyage->getDateRetour()
            || $ancien->getNbrPersonne() !== $voyage->getNbrPersonne()
            || $ancien->getBudgetPersonne() !== $voyage->getBudgetPersonne()
            || $ancien->getTypeDestination() !== $voyage->getTypeDestination();

        $choixAnnule = $criteresModifies && ($ancien->getIdVol() !== null || $ancien->getIdHotel() !== null);

        $sql = 'UPDATE voyage SET
                    nom = :nom,
                    type_voyage = :type_voyage,
                    nbr_personne = :nbr_personne,
                    budget_personne = :budget_personne,
                    date_aller = :date_aller,
                    date_retour = :date_retour,
                    type_destination = :type_destination';

        if ($choixAnnule) {
            $sql .= ", id_vol = NULL, id_hotel = NULL, statut = 'en_preparation'";
        }

        $sql .= ' WHERE id = :id AND id_utilisateur = :id_utilisateur';

        $stmt = $this->database->getPdo()->prepare($sql);
        $this->lierCriteres($stmt, $voyage);
        $stmt->bindValue(':id', $voyage->getId());
        $stmt->bindValue(':id_utilisateur', $voyage->getIdUtilisateur());
        $stmt->execute();

        return $choixAnnule;
    }

    // Supprime un voyage de cet utilisateur et ses tâches.
    // Les tâches d'abord : la clé étrangère taches.id_voyage interdit de supprimer
    // un voyage qui a encore des tâches.
    public function removeVoyage(int $idVoyage, int $idUtilisateur): bool {
        // On vérifie d'abord que le voyage appartient bien à cet utilisateur
        if ($this->getVoyageUtilisateur($idVoyage, $idUtilisateur) === null) {
            return false;
        }

        // 1. Supprimer les tâches du voyage
        $stmt = $this->database->getPdo()->prepare('DELETE FROM taches WHERE id_voyage = :id');
        $stmt->bindValue(':id', $idVoyage);
        $stmt->execute();

        // 2. Supprimer le voyage
        $stmt = $this->database->getPdo()->prepare('DELETE FROM voyage WHERE id = :id AND id_utilisateur = :id_utilisateur');
        $stmt->bindValue(':id', $idVoyage);
        $stmt->bindValue(':id_utilisateur', $idUtilisateur);
        $stmt->execute();

        return true;
    }

    // Lie les critères communs à l'INSERT et à l'UPDATE
    private function lierCriteres(\PDOStatement $stmt, Voyage $voyage): void {
        $stmt->bindValue(':nom', $voyage->getNom());
        $stmt->bindValue(':type_voyage', $voyage->getTypeVoyage());
        $stmt->bindValue(':nbr_personne', $voyage->getNbrPersonne());
        $stmt->bindValue(':budget_personne', $voyage->getBudgetPersonne());
        $stmt->bindValue(':date_aller', $voyage->getDateAller()->format('Y-m-d'));
        $stmt->bindValue(':date_retour', $voyage->getDateRetour()->format('Y-m-d'));
        $stmt->bindValue(':type_destination', $voyage->getTypeDestination());
    }

    // Transforme une ligne de la base en objet Voyage
    private function creerVoyage(array $ligne): Voyage {
        $voyage = new Voyage();
        $voyage->setId((int) $ligne['id']);
        $voyage->setNom($ligne['nom']);
        $voyage->setTypeVoyage($ligne['type_voyage']);
        $voyage->setNbrPersonne((int) $ligne['nbr_personne']);
        $voyage->setBudgetPersonne((float) $ligne['budget_personne']);
        $voyage->setDateAller(new DateTime($ligne['date_aller']));
        $voyage->setDateRetour(new DateTime($ligne['date_retour']));
        $voyage->setStatut($ligne['statut'] ?? 'en_preparation');
        $voyage->setTypeDestination($ligne['type_destination']);
        $voyage->setIdUtilisateur((int) $ligne['id_utilisateur']);
        $voyage->setIdVol($ligne['id_vol'] !== null ? (int) $ligne['id_vol'] : null);
        $voyage->setIdHotel($ligne['id_hotel'] !== null ? (int) $ligne['id_hotel'] : null);

        return $voyage;
    }
}
