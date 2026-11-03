<?php
// Gère la langue du site (i18n).
// - La langue se choisit avec ?lang=fr ou ?lang=en dans l'URL, puis est gardée en session.
// - Les textes de l'interface sont dans src/lang/fr.php et src/lang/en.php.
// - Les données de la base traduites (ville_en, pays_en...) sont choisies avec Lang::champ().

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

    // Choisit la bonne colonne d'une ligne de la base selon la langue.
    // Lang::champ($destination, 'ville') retourne 'ville_en' en anglais, 'ville' en français.
    // Si la traduction est vide, on garde la version française.
    public static function champ(array $ligne, string $colonne): string {
        self::initialiser();

        if (self::$langue !== 'fr') {
            $traduction = $ligne[$colonne . '_' . self::$langue] ?? '';
            if ($traduction !== '') {
                return $traduction;
            }
        }

        return (string) ($ligne[$colonne] ?? '');
    }
}
