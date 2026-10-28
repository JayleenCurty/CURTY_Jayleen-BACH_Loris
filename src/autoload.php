<?php
// Chargement automatique des classes.
// Quand PHP rencontre une classe qu'il ne connaît pas encore (ex. new UtilisateurManager(...)),
// il appelle cette fonction avec le nom de la classe, et on charge le fichier correspondant.
// Règle : une classe = un fichier du même nom dans src/classes/
spl_autoload_register(function (string $classe): void {
    $fichier = __DIR__ . '/classes/' . $classe . '.php';

    if (file_exists($fichier)) {
        require_once $fichier;
    }
});
