<?php
require_once __DIR__ . '/../src/autoload.php';

// Page privée
Auth::exigerConnexion();

$idUtilisateur = Auth::utilisateur()['id'];

$database      = new Database();
$voyageManager = new VoyageManager($database->getPdo());

// L'id du voyage arrive par l'URL (lien depuis Mes voyages) ou par le champ caché du formulaire
$idVoyage = (int) ($_POST['voyage'] ?? $_GET['voyage'] ?? 0);

// Le voyage doit exister ET appartenir à la personne connectée
$voyageEnBase = $voyageManager->trouverPourUtilisateur($idVoyage, $idUtilisateur);

if ($voyageEnBase === null) {
    header('Location: mes-voyages.php');
    exit;
}

// Premier affichage : le formulaire est pré-rempli avec les valeurs de la base
// (array_intersect_key garde seulement les champs du formulaire)
$voyage  = array_intersect_key($voyageEnBase, VoyageFormulaire::CHAMPS_VIDES);
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mêmes étapes que pour la création, avec les mêmes règles
    $voyage  = VoyageFormulaire::lire($_POST);
    $erreurs = VoyageFormulaire::valider($voyage);

    if (count($erreurs) === 0) {
        $choixAnnule = $voyageManager->modifier($idVoyage, $idUtilisateur, VoyageFormulaire::preparer($voyage));

        header('Location: mes-voyages.php?modifie=' . ($choixAnnule ? 'choix_annule' : 'ok'));
        exit;
    }
}

// Le budget vient de la base avec des décimales (750.00) : on l'affiche sans
if (is_numeric($voyage['budget_personne'])) {
    $voyage['budget_personne'] = (string) (float) $voyage['budget_personne'];
}

$titrePage = Lang::t('modifier.titre_page');
$cssPage   = 'formulaires';
require __DIR__ . '/../src/partials/header.php';
?>

<main class="page">
    <section class="carte-formulaire carte-large">
        <h1><?= htmlspecialchars(Lang::t('modifier.titre')) ?></h1>
        <p class="sous-titre"><?= htmlspecialchars(Lang::t('modifier.sous_titre')) ?></p>

        <?php
        // Formulaire commun avec voyage-creer.php
        $actionFormulaire = 'voyage-modifier.php';
        $texteBouton      = Lang::t('modifier.bouton');
        require __DIR__ . '/../src/partials/formulaire-voyage.php';
        ?>

        <p class="bas-formulaire">
            <a href="mes-voyages.php"><?= htmlspecialchars(Lang::t('modifier.annuler')) ?></a>
        </p>
    </section>
</main>

<?php require __DIR__ . '/../src/partials/footer.php'; ?>
