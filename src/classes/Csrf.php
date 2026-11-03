<?php
// Protection CSRF (Cross-Site Request Forgery).
// Un autre site pourrait envoyer un formulaire vers le nôtre à la place de la personne connectée
// (ex. supprimer un voyage à son insu). Pour l'en empêcher, chaque formulaire sensible contient
// un jeton secret, stocké en session : sans le bon jeton, l'action est refusée.

class Csrf {
    private static function demarrerSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Jeton de la session (créé au premier appel), à mettre dans un champ caché du formulaire
    public static function jeton(): string {
        self::demarrerSession();

        if (!isset($_SESSION['csrf'])) {
            // 32 octets aléatoires, impossibles à deviner
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf'];
    }

    // Vrai si le jeton envoyé par le formulaire est celui de la session
    public static function verifier(?string $jetonRecu): bool {
        self::demarrerSession();

        // hash_equals compare en temps constant (bonne pratique pour les secrets)
        return is_string($jetonRecu)
            && isset($_SESSION['csrf'])
            && hash_equals($_SESSION['csrf'], $jetonRecu);
    }
}
