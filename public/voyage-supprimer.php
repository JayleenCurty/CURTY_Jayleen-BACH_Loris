<?php
require_once __DIR__ . '/../src/autoload.php';

// Page privée
Auth::exigerConnexion();

$idUtilisateur = Auth::utilisateur()['id'];

$database      = new Database();
$voyageManager = new VoyageManager($database->getPdo());

// L'id du voyage arrive par l'URL (lien depuis Mes voyages) ou par le formulaire (POST)
$idVoyage = (int) ($_POST['voyage'] ?? $_GET['voyage'] ?? 0);

// Le voyage doit exister ET appartenir à la personne connectée
$voyage = $voyageManager->trouverPourUtilisateur($idVoyage, $idUtilisateur);

if ($voyage === null) {
    // Voyage inexistant ou d'un autre utilisateur : on ne dit pas lequel des deux
    header('Location: mes-voyages.php');
    exit;
}

$erreur = '';

// La suppression ne se fait QUE par le formulaire (POST), jamais par un simple lien (GET)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::verifier($_POST['csrf'] ?? null)) {
        $erreur = Lang::t('supprimer.err_jeton');
    } else {
        $voyageManager->supprimer($idVoyage, $idUtilisateur);

        header('Location: mes-voyages.php?supprime=ok');
        exit;
    }
}

$titrePage = Lang::t('supprimer.titre_page');
$cssPage   = 'formulaires';
require __DIR__ . '/../src/partials/header.php';
?>

<main class="page">
    <section class="carte-formulaire">
        <h1><?= htmlspecialchars(Lang::t('supprimer.titre')) ?></h1>
        <p class="sous-titre"><?= htmlspecialchars(Lang::t('supprimer.texte', ['nom' => $voyage['nom']])) ?></p>

        <?php if ($erreur !== ''): ?>
            <p class="erreur erreur-globale"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="post" action="voyage-supprimer.php">
            <!-- Champs cachés : quel voyage, et le jeton anti-CSRF -->
            <input type="hidden" name="voyage" value="<?= (int) $voyage['id'] ?>" />
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(Csrf::jeton()) ?>" />

            <div class="actions-confirmation">
                <button type="submit" class="btn btn-danger"><?= htmlspecialchars(Lang::t('supprimer.confirmer')) ?></button>
                <a href="mes-voyages.php" class="btn btn-secondaire"><?= htmlspecialchars(Lang::t('supprimer.annuler')) ?></a>
            </div>
        </form>
    </section>
</main>

<?php require __DIR__ . '/../src/partials/footer.php'; ?>
