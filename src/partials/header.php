<?php
// En-tête commun à toutes les pages.
// La page peut définir $titrePage et $cssPage avant de l'inclure.
// Sinon, on utilise des valeurs par défaut.
$titrePage = $titrePage ?? 'GroupTrip';
$cssPage   = $cssPage ?? 'base';

$utilisateurConnecte = Auth::utilisateur();
$langue              = Lang::code();
?>
<!doctype html>
<html lang="<?= $langue ?>">

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
                <span class="bonjour"><?= htmlspecialchars(Lang::t('nav.bonjour', ['prenom' => $utilisateurConnecte['prenom']])) ?></span>
                <a href="mes-voyages.php"><?= htmlspecialchars(Lang::t('nav.mes_voyages')) ?></a>
                <a href="deconnexion.php"><?= htmlspecialchars(Lang::t('nav.deconnexion')) ?></a>
            <?php else: ?>
                <a href="connexion.php"><?= htmlspecialchars(Lang::t('nav.connexion')) ?></a>
                <a href="inscription.php"><?= htmlspecialchars(Lang::t('nav.inscription')) ?></a>
            <?php endif; ?>

            <span class="langues">
                <a href="?lang=fr" class="<?= $langue === 'fr' ? 'langue-active' : '' ?>">FR</a>
                <a href="?lang=en" class="<?= $langue === 'en' ? 'langue-active' : '' ?>">EN</a>
            </span>
        </nav>
    </header>
