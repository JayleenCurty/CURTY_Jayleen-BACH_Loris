<?php
// Regroupe toutes les requêtes SQL sur la table voyage.

class VoyageManager {
    // Valeurs autorisées : les mêmes que les ENUM de la table voyage (01-schema.sql)
    public const TYPES_VOYAGE      = ['amis', 'evjf_evg', 'etudes', 'seminaire', 'autre'];
    public const TYPES_DESTINATION = ['plage', 'ville', 'montagne', 'nature'];

    // Limites du nombre de participant·es (le minimum correspond au CHECK de la base)
    public const PERSONNES_MIN = 2;
    public const PERSONNES_MAX = 30;

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Crée un voyage pour un utilisateur et retourne son id.
    // $donnees contient les champs déjà validés par la page.
    public function creer(int $idUtilisateur, array $donnees): int {
        $requete = $this->pdo->prepare(
            'INSERT INTO voyage
                (nom, type_voyage, nbr_personne, budget_personne,
                 date_aller, date_retour, type_destination, id_utilisateur)
             VALUES
                (:nom, :type_voyage, :nbr_personne, :budget_personne,
                 :date_aller, :date_retour, :type_destination, :id_utilisateur)'
        );

        $requete->execute([
            'nom'              => $donnees['nom'],
            'type_voyage'      => $donnees['type_voyage'],
            'nbr_personne'     => $donnees['nbr_personne'],
            'budget_personne'  => $donnees['budget_personne'],
            'date_aller'       => $donnees['date_aller'],
            'date_retour'      => $donnees['date_retour'],
            'type_destination' => $donnees['type_destination'],
            'id_utilisateur'   => $idUtilisateur,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    // Tous les voyages d'un utilisateur, du départ le plus proche au plus lointain.
    // LEFT JOIN : on garde les voyages dont le vol ou l'hôtel n'est pas encore choisi
    // (id_vol / id_hotel à NULL). La destination est connue à travers le vol choisi.
    public function listerPourUtilisateur(int $idUtilisateur): array {
        $requete = $this->pdo->prepare(
            'SELECT v.id, v.nom, v.type_voyage, v.nbr_personne, v.budget_personne,
                    v.date_aller, v.date_retour, v.statut, v.type_destination,
                    d.ville, d.ville_en, d.pays, d.pays_en,
                    h.nom AS hotel_nom
             FROM voyage v
             LEFT JOIN vol vo        ON vo.id = v.id_vol
             LEFT JOIN destination d ON d.id = vo.id_destination
             LEFT JOIN hotel h       ON h.id = v.id_hotel
             WHERE v.id_utilisateur = :id_utilisateur
             ORDER BY v.date_aller'
        );
        $requete->execute(['id_utilisateur' => $idUtilisateur]);

        return $requete->fetchAll();
    }

    // Un voyage, seulement s'il appartient à cet utilisateur (sinon null).
    // On filtre sur les deux id : changer le numéro dans l'URL ne donne pas accès au voyage d'un autre.
    public function trouverPourUtilisateur(int $idVoyage, int $idUtilisateur): ?array {
        $requete = $this->pdo->prepare(
            'SELECT * FROM voyage WHERE id = :id AND id_utilisateur = :id_utilisateur'
        );
        $requete->execute(['id' => $idVoyage, 'id_utilisateur' => $idUtilisateur]);
        $voyage = $requete->fetch();

        return $voyage === false ? null : $voyage;
    }

    // Supprime un voyage de cet utilisateur et ses tâches.
    // Les tâches d'abord : la clé étrangère taches.id_voyage interdit de supprimer
    // un voyage qui a encore des tâches.
    // La transaction garantit que tout est supprimé, ou rien si une erreur survient.
    public function supprimer(int $idVoyage, int $idUtilisateur): bool {
        if ($this->trouverPourUtilisateur($idVoyage, $idUtilisateur) === null) {
            return false;
        }

        $this->pdo->beginTransaction();

        try {
            $requete = $this->pdo->prepare('DELETE FROM taches WHERE id_voyage = :id');
            $requete->execute(['id' => $idVoyage]);

            $requete = $this->pdo->prepare(
                'DELETE FROM voyage WHERE id = :id AND id_utilisateur = :id_utilisateur'
            );
            $requete->execute(['id' => $idVoyage, 'id_utilisateur' => $idUtilisateur]);

            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        return true;
    }
}
