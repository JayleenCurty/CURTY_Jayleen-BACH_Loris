<?php
require_once __DIR__ . '/../src/autoload.php';

// Page privée : on renvoie vers la connexion si personne n'est connecté
Auth::exigerConnexion();

$utilisateur = Auth::utilisateur();

$titrePage = Lang::t('mes_voyages.titre_page');
$cssPage   = 'formulaires';
require __DIR__ . '/../src/partials/header.php';
?>

<main class="page">
    <h1><?= htmlspecialchars(Lang::t('mes_voyages.titre')) ?></h1>
    <p><?= htmlspecialchars(Lang::t('mes_voyages.connecte', ['prenom' => $utilisateur['prenom']])) ?></p>
</main>

<?php require __DIR__ . '/../src/partials/footer.php'; ?>
