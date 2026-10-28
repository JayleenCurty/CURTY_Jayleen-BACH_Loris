<?php
// Gère la session de la personne connectée : connexion, déconnexion, vérifications.
// Toutes les méthodes sont "static" : on les appelle directement avec Auth::methode(),
// sans faire de new Auth(). Il n'y a qu'une seule session par visiteur, donc pas besoin d'objet.

class Auth {
    // Démarre la session PHP si ce n'est pas déjà fait
    private static function demarrerSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Enregistre l'utilisateur dans la session après un mot de passe correct
    public static function connecter(array $utilisateur): void {
        self::demarrerSession();

        // Nouvel identifiant de session à chaque connexion (protège contre le vol de session)
        session_regenerate_id(true);

        $_SESSION['utilisateur'] = [
            'id'     => (int) $utilisateur['id'],
            'prenom' => $utilisateur['prenom'],
            'role'   => $utilisateur['role'],
        ];
    }

    // Vide et détruit la session
    public static function deconnecter(): void {
        self::demarrerSession();

        $_SESSION = [];
        session_destroy();
    }

    public static function estConnecte(): bool {
        self::demarrerSession();

        return isset($_SESSION['utilisateur']);
    }

    // Données de la personne connectée (ou null si personne)
    public static function utilisateur(): ?array {
        self::demarrerSession();

        return $_SESSION['utilisateur'] ?? null;
    }

    // À appeler en haut des pages privées : renvoie vers la connexion si besoin
    public static function exigerConnexion(): void {
        if (!self::estConnecte()) {
            header('Location: connexion.php');
            exit;
        }
    }
}
