<?php
// Gère la langue du site (i18n).
// - La langue se choisit avec ?lang=fr ou ?lang=en dans l'URL, puis est gardée en session.
// - Les textes de l'interface sont dans src/lang/fr.php et src/lang/en.php.
// - Les données de la base traduites (ville_en, pays_en...) sont choisies avec Lang::choisir().

class Lang {
    // Langues disponibles : la première est celle par défaut
    private const LANGUES = ['fr', 'en'];

    private static ?string $langue = null;
    private static array $textes = [];

    // Détermine la langue et charge le bon fichier (une seule fois par page)
    private static function initialiser(): void {
        if (self::$langue !== null) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. La personne vient de cliquer sur FR / EN : on mémorise son choix
        $demandee = $_GET['lang'] ?? null;
        if (in_array($demandee, self::LANGUES, true)) {
            $_SESSION['langue'] = $demandee;
        }

        // 2. Sinon on reprend la langue de la session, ou la langue par défaut
        $langue = $_SESSION['langue'] ?? self::LANGUES[0];

        // Sécurité : on n'accepte qu'une langue de la liste (jamais une valeur libre)
        if (!in_array($langue, self::LANGUES, true)) {
            $langue = self::LANGUES[0];
        }

        self::$langue = $langue;
        self::$textes = require __DIR__ . '/../lang/' . $langue . '.php';
    }

    // Code de la langue actuelle : 'fr' ou 'en'
    public static function code(): string {
        self::initialiser();

        return self::$langue;
    }

    // Texte traduit pour une clé.
    // $valeurs remplace les {mots} du texte : Lang::t('nav.bonjour', ['prenom' => 'Loris'])
    public static function t(string $cle, array $valeurs = []): string {
        self::initialiser();

        // Clé absente : on affiche la clé elle-même pour repérer l'oubli
        $texte = self::$textes[$cle] ?? $cle;

        foreach ($valeurs as $nom => $valeur) {
            $texte = str_replace('{' . $nom . '}', (string) $valeur, $texte);
        }

        return $texte;
    }

    // Choisit entre la version française et la version anglaise d'une donnée de la base.
    // Lang::choisir($destination->getVille(), $destination->getVilleEn())
    // retourne la ville en anglais si le site est en anglais et que la traduction existe.
    public static function choisir(string $francais, ?string $anglais): string {
        self::initialiser();

        if (self::$langue === 'en' && $anglais !== null && $anglais !== '') {
            return $anglais;
        }

        return $francais;
    }

    // Date affichée au format de la langue :
    // 14.06.2027 en français, 14 Jun 2027 en anglais
    // Accepte un objet DateTime (venant d'une entité) ou une date texte AAAA-MM-JJ.
    public static function date(DateTimeInterface|string $date): string {
        self::initialiser();

        if (is_string($date)) {
            $date = new DateTime($date);
        }

        return self::$langue === 'en' ? $date->format('j M Y') : $date->format('d.m.Y');
    }
}
