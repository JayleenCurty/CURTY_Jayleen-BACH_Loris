<?php

namespace Utilisateurs;

use Database;

// Regroupe toutes les requêtes SQL sur la table utilisateur.
class UtilisateursManager {
    private Database $database;

    public function __construct() {
        $this->database = new Database();
    }

    // Retourne l'utilisateur qui a cet e-mail, ou null s'il n'existe pas
    public function getUtilisateurParEmail(string $email): ?Utilisateur {
        $sql = 'SELECT * FROM utilisateur WHERE email = :email';

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $ligne = $stmt->fetch();

        // fetch() retourne false quand il n'y a aucune ligne
        if ($ligne === false) {
            return null;
        }

        return $this->creerUtilisateur($ligne);
    }

    // Vrai si un compte utilise déjà cette adresse e-mail
    public function emailExiste(string $email): bool {
        $sql = 'SELECT COUNT(*) FROM utilisateur WHERE email = :email';

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    // Enregistre un nouvel utilisateur et retourne son id
    public function addUtilisateur(Utilisateur $utilisateur): int {
        $sql = 'INSERT INTO utilisateur (prenom, nom, email, mot_de_passe, role)
                VALUES (:prenom, :nom, :email, :mot_de_passe, :role)';

        $stmt = $this->database->getPdo()->prepare($sql);
        $stmt->bindValue(':prenom', $utilisateur->getPrenom());
        $stmt->bindValue(':nom', $utilisateur->getNom());
        $stmt->bindValue(':email', $utilisateur->getEmail());
        $stmt->bindValue(':mot_de_passe', $utilisateur->getMotDePasse());
        $stmt->bindValue(':role', $utilisateur->getRole());
        $stmt->execute();

        return (int) $this->database->getPdo()->lastInsertId();
    }

    // Transforme une ligne de la base (tableau) en objet Utilisateur
    private function creerUtilisateur(array $ligne): Utilisateur {
        $utilisateur = new Utilisateur();
        $utilisateur->setId((int) $ligne['id']);
        $utilisateur->setPrenom($ligne['prenom']);
        $utilisateur->setNom($ligne['nom']);
        $utilisateur->setEmail($ligne['email']);
        $utilisateur->setMotDePasse($ligne['mot_de_passe']);
        $utilisateur->setRole($ligne['role']);

        return $utilisateur;
    }
}
