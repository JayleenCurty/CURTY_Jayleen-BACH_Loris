<?php
require_once __DIR__ . '/../src/utils/autoloader.php';

use Voyages\Voyage;
use Voyages\VoyagesManager;

// Page privée
Auth::exigerConnexion();

$voyagesManager = new VoyagesManager();

// Ce qui est affiché dans les champs (vide au premier affichage)
$saisie  = [];
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Garder la saisie pour la réafficher en cas d'erreur
    $saisie = $_POST;

    $voyage = new Voyage();

    // 2. Valider : chaque setter de Voyage vérifie sa valeur et lance une
    //    InvalidArgumentException si elle n'est pas valide. Un try/catch par champ
    //    permet d'afficher TOUTES les erreurs, chacune sous son champ.
    try {
        $voyage->setNom($_POST['nom'] ?? '');
    } catch (InvalidArgumentException $e) {
        $erreurs['nom'] = $e->getMessage();
    }

    try {
        $voyage->setTypeVoyage($_POST['type_voyage'] ?? '');
    } catch (InvalidArgumentException $e) {
        $erreurs['type_voyage'] = $e->getMessage();
    }

    try {
        $voyage->setTypeDestination($_POST['type_destination'] ?? '');
    } catch (InvalidArgumentException $e) {
        $erreurs['type_destination'] = $e->getMessage();
    }

    try {
        // (int) transforme le texte du formulaire en nombre entier ("abc" devient 0, refusé par le setter)
        $voyage->setNbrPersonne((int) ($_POST['nbr_personne'] ?? 0));
    } catch (InvalidArgumentException $e) {
        $erreurs['nbr_personne'] = $e->getMessage();
    }

    try {
        $voyage->setBudgetPersonne((float) ($_POST['budget_personne'] ?? 0));
    } catch (InvalidArgumentException $e) {
        $erreurs['budget_personne'] = $e->getMessage();
    }

    // Dates : new DateTime() lance une Exception si la date n'existe pas (ex. 2027-13-45)
    try {
        if (($_POST['date_aller'] ?? '') === '') {
            throw new Exception();
        }
        $dateAller = new DateTime($_POST['date_aller']);

        // Règle du formulaire : on ne crée pas un voyage dans le passé
        if ($dateAller < new DateTime('today')) {
            throw new Exception();
        }
        $voyage->setDateAller($dateAller);
    } catch (Exception $e) {
        $erreurs['date_aller'] = Lang::t('voyage.err_date_aller');
    }

    try {
        if (($_POST['date_retour'] ?? '') === '') {
            throw new InvalidArgumentException(Lang::t('voyage.err_date_retour'));
        }
        // setDateRetour vérifie que le retour est après le départ
        $voyage->setDateRetour(new DateTime($_POST['date_retour']));
    } catch (InvalidArgumentException $e) {
        $erreurs['date_retour'] = $e->getMessage();
    } catch (Exception $e) {
        $erreurs['date_retour'] = Lang::t('voyage.err_date_retour');
    }

    // 3. Tout est valide : on enregistre et on redirige
    if (count($erreurs) === 0) {
        $voyage->setIdUtilisateur(Auth::utilisateur()['id']);
        $voyagesManager->addVoyage($voyage);

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
