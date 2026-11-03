<?php
require_once __DIR__ . '/../src/autoload.php';

// Page privée
Auth::exigerConnexion();

$database       = new Database();
$voyageManager  = new VoyageManager($database->getPdo());

// Valeurs du formulaire (vides au premier affichage)
$voyage  = VoyageFormulaire::CHAMPS_VIDES;
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Récupérer et nettoyer les saisies
    $voyage = VoyageFormulaire::lire($_POST);

    // 2. Valider côté serveur (règles communes avec voyage-modifier.php)
    $erreurs = VoyageFormulaire::valider($voyage);

    // 3. Tout est valide : on enregistre et on redirige
    if (count($erreurs) === 0) {
        $voyageManager->creer(Auth::utilisateur()['id'], VoyageFormulaire::preparer($voyage));

        // Plus tard : redirection vers propositions.php?voyage=<id>
        header('Location: mes-voyages.php?cree=ok');
        exit;
    }
}

$titrePage = Lang::t('voyage.titre_page');
$cssPage   = 'formulaires';
require __DIR__ . '/../src/partials/header.php';
?>

<main class="page">
    <section class="carte-formulaire carte-large">
        <h1><?= htmlspecialchars(Lang::t('voyage.titre')) ?></h1>
        <p class="sous-titre"><?= htmlspecialchars(Lang::t('voyage.sous_titre')) ?></p>

        <?php
        // Formulaire commun avec voyage-modifier.php
        $actionFormulaire = 'voyage-creer.php';
        $texteBouton      = Lang::t('voyage.bouton');
        require __DIR__ . '/../src/partials/formulaire-voyage.php';
        ?>
    </section>
</main>

<?php require __DIR__ . '/../src/partials/footer.php'; ?>
