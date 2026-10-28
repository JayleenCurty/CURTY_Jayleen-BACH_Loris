<?php
// En-tête commun à toutes les pages.
// La page peut définir $titrePage et $cssPage avant de l'inclure.
// Sinon, on utilise des valeurs par défaut.
$titrePage = $titrePage ?? 'Accueil';
$cssPage   = $cssPage ?? 'base';

$utilisateurConnecte = Auth::utilisateur();
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($titrePage) ?> - GroupTrip</title>
    <link rel="stylesheet" href="./css/reset.css" />
    <link rel="stylesheet" href="./css/base.css" />
    <link rel="stylesheet" href="./css/<?= htmlspecialchars($cssPage) ?>.css" />
</head>

<body>
    <header class="site-header">
        <a href="index.php" class="logo">GroupTrip</a>
        <nav>
            <?php if ($utilisateurConnecte !== null): ?>
                <span class="bonjour">Bonjour <?= htmlspecialchars($utilisateurConnecte['prenom']) ?></span>
                <a href="mes-voyages.php">Mes voyages</a>
                <a href="deconnexion.php">Déconnexion</a>
            <?php else: ?>
                <a href="connexion.php">Connexion</a>
                <a href="inscription.php">Inscription</a>
            <?php endif; ?>
        </nav>
    </header>
