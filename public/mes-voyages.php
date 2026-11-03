<?php
require_once __DIR__ . '/../src/autoload.php';

// Page privée : on renvoie vers la connexion si personne n'est connecté
Auth::exigerConnexion();

$utilisateur = Auth::utilisateur();

// Message après la création d'un voyage (voyage-creer.php redirige vers ?cree=ok)
$voyageCree = ($_GET['cree'] ?? '') === 'ok';

$titrePage = Lang::t('mes_voyages.titre_page');
$cssPage   = 'formulaires';
require __DIR__ . '/../src/partials/header.php';
?>

<main class="page">
    <h1><?= htmlspecialchars(Lang::t('mes_voyages.titre')) ?></h1>
    <p><?= htmlspecialchars(Lang::t('mes_voyages.connecte', ['prenom' => $utilisateur['prenom']])) ?></p>

    <?php if ($voyageCree): ?>
        <p class="succes"><?= htmlspecialchars(Lang::t('mes_voyages.cree')) ?></p>
    <?php endif; ?>

    <p><a href="voyage-creer.php" class="btn"><?= htmlspecialchars(Lang::t('mes_voyages.nouveau')) ?></a></p>
</main>

<?php require __DIR__ . '/../src/partials/footer.php'; ?>
