<?php
require_once __DIR__ . '/../src/utils/autoloader.php';

use Voyages\VoyagesManager;

// Page privée
Auth::exigerConnexion();

$idUtilisateur = Auth::utilisateur()['id'];

$voyagesManager = new VoyagesManager();

// L'id du voyage arrive par l'URL (lien depuis Mes voyages) ou par le champ caché du formulaire
$idVoyage = (int) ($_POST['voyage'] ?? $_GET['voyage'] ?? 0);

// Le voyage doit exister ET appartenir à la personne connectée (objet Voyage ou null)
$voyage = $voyagesManager->getVoyageUtilisateur($idVoyage, $idUtilisateur);

if ($voyage === null) {
    header('Location: mes-voyages.php');
    exit;
}

// Premier affichage : les champs sont pré-remplis avec les valeurs du voyage (getters)
$saisie = [
    'nom'              => $voyage->getNom(),
    'type_voyage'      => $voyage->getTypeVoyage(),
    'type_destination' => $voyage->getTypeDestination(),
    'nbr_personne'     => (string) $voyage->getNbrPersonne(),
    'budget_personne'  => (string) $voyage->getBudgetPersonne(),   // 750.00 en base → "750"
    'date_aller'       => $voyage->getDateAller()->format('Y-m-d'),
    'date_retour'      => $voyage->getDateRetour()->format('Y-m-d'),
];
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $saisie = $_POST;


    // Mêmes règles que pour la création, appliquées à l'objet Voyage existant
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

    if (count($erreurs) === 0) {
        $choixAnnule = $voyagesManager->updateVoyage($voyage);

        header('Location: mes-voyages.php?modifie=' . ($choixAnnule ? 'choix_annule' : 'ok'));
        exit;
    }
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
