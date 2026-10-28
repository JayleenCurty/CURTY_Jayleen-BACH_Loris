<?php
// Regroupe toutes les requêtes SQL sur la table utilisateur.

class UtilisateurManager {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Vrai si un compte utilise déjà cette adresse e-mail
    public function emailExiste(string $email): bool {
        $requete = $this->pdo->prepare('SELECT COUNT(*) FROM utilisateur WHERE email = :email');
        $requete->execute(['email' => $email]);

        return (int) $requete->fetchColumn() > 0;
    }

    // Crée un compte (rôle "user" par défaut) et retourne son id
    public function creer(string $prenom, string $nom, string $email, string $motDePasse): int {
        $requete = $this->pdo->prepare(
            'INSERT INTO utilisateur (prenom, nom, email, mot_de_passe)
             VALUES (:prenom, :nom, :email, :mot_de_passe)'
        );

        $requete->execute([
            'prenom'       => $prenom,
            'nom'          => $nom,
            'email'        => $email,
            // On ne stocke jamais le mot de passe en clair, seulement son hash
            'mot_de_passe' => password_hash($motDePasse, PASSWORD_DEFAULT),
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}
