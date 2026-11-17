<?php
// Chargement automatique des classes (même principe que l'autoloader du cours).
// Quand PHP rencontre une classe inconnue, il appelle cette fonction avec son nom complet,
// namespace compris. Exemples :
//   Database             → src/classes/Database.php
//   Voyages\Voyage       → src/classes/Voyages/Voyage.php
//   Voyages\VoyagesManager → src/classes/Voyages/VoyagesManager.php
// Règle : le namespace correspond au dossier, le nom de la classe au nom du fichier.
spl_autoload_register(function (string $classe): void {
    // Convertit les séparateurs de namespace (\) en séparateurs de dossiers (/)
    $cheminRelatif = str_replace('\\', '/', $classe);

    $fichier = __DIR__ . '/../classes/' . $cheminRelatif . '.php';

    if (file_exists($fichier)) {
        require_once $fichier;
    }
});
