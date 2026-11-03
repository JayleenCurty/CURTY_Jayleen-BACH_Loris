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
}
