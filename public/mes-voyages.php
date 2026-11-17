<?php
require_once __DIR__ . '/../src/utils/autoloader.php';

use Destinations\DestinationsManager;
use Hotels\HotelsManager;
use Vols\VolsManager;
use Voyages\VoyagesManager;

// Page privée : on renvoie vers la connexion si personne n'est connecté
Auth::exigerConnexion();

$utilisateur = Auth::utilisateur();

$voyagesManager      = new VoyagesManager();
$volsManager         = new VolsManager();
$destinationsManager = new DestinationsManager();
$hotelsManager       = new HotelsManager();

// Uniquement les voyages de la personne connectée (tableau d'objets Voyage)
$voyages = $voyagesManager->getVoyagesUtilisateur($utilisateur['id']);

// Pour chaque voyage, la destination (connue à travers le vol choisi) et l'hôtel, s'ils sont choisis
$details = [];
foreach ($voyages as $voyage) {
    $destination = null;
    $hotel       = null;

    if ($voyage->getIdVol() !== null) {
        $vol = $volsManager->getVol($voyage->getIdVol());
        if ($vol !== null) {
            $destination = $destinationsManager->getDestination($vol->getIdDestination());
        }
    }

    if ($voyage->getIdHotel() !== null) {
        $hotel = $hotelsManager->getHotel($voyage->getIdHotel());
    }

    $details[$voyage->getId()] = ['destination' => $destination, 'hotel' => $hotel];
}

// Messages après une action (voyage-creer.php redirige vers ?cree=ok, etc.)
$voyageCree = ($_GET['cree'] ?? '') === 'ok';
$voyageSupprime = ($_GET['supprime'] ?? '') === 'ok';
$voyageModifie  = $_GET['modifie'] ?? '';   // 'ok' ou 'choix_annule'

$titrePage = Lang::t('mes_voyages.titre_page');
$cssPage   = 'mes-voyages';
require __DIR__ . '/../src/partials/header.php';
?>

<main class="page">
    <div class="entete-liste">
        <h1><?= htmlspecialchars(Lang::t('mes_voyages.titre')) ?></h1>
        <a href="voyage-creer.php" class="btn"><?= htmlspecialchars(Lang::t('mes_voyages.nouveau')) ?></a>
    </div>

    <?php if ($voyageCree): ?>
        <p class="succes"><?= htmlspecialchars(Lang::t('mes_voyages.cree')) ?></p>
    <?php endif; ?>

    <?php if ($voyageSupprime): ?>
        <p class="succes"><?= htmlspecialchars(Lang::t('mes_voyages.supprime')) ?></p>
    <?php endif; ?>

    <?php if ($voyageModifie === 'ok'): ?>
        <p class="succes"><?= htmlspecialchars(Lang::t('mes_voyages.modifie')) ?></p>
    <?php elseif ($voyageModifie === 'choix_annule'): ?>
        <p class="succes"><?= htmlspecialchars(Lang::t('mes_voyages.modifie_choix_annule')) ?></p>
    <?php endif; ?>

    <?php if (count($voyages) === 0): ?>
        <p class="vide"><?= htmlspecialchars(Lang::t('mes_voyages.aucun')) ?></p>
    <?php else: ?>
        <div class="liste-voyages">
            <?php foreach ($voyages as $v): ?>
                <?php $destination = $details[$v->getId()]['destination']; ?>
                <?php $hotel = $details[$v->getId()]['hotel']; ?>
                <article class="voyage">
                    <div class="voyage-principal">
                        <p class="voyage-trajet">
                            GVA <span>✈</span>
                            <?php if ($destination !== null): ?>
                                <?= htmlspecialchars(Lang::choisir($destination->getVille(), $destination->getVilleEn())) ?>
                            <?php else: ?>
                                ???
                            <?php endif; ?>
                        </p>

                        <h2><?= htmlspecialchars($v->getNom()) ?></h2>

                        <p class="voyage-infos">
                            <?= htmlspecialchars(Lang::date($v->getDateAller())) ?> – <?= htmlspecialchars(Lang::date($v->getDateRetour())) ?>
                            · <?= htmlspecialchars(Lang::t('mes_voyages.personnes', ['nombre' => $v->getNbrPersonne()])) ?>
                            · <?= htmlspecialchars(Lang::t('mes_voyages.budget', ['montant' => number_format($v->getBudgetPersonne(), 0, '.', "'")])) ?>
                        </p>

                        <p class="voyage-infos">
                            <?= htmlspecialchars(Lang::t('type_voyage.' . $v->getTypeVoyage())) ?>
                            · <?= htmlspecialchars(ucfirst(Lang::t('type_destination.' . $v->getTypeDestination()))) ?>
                            <?php if ($hotel !== null): ?>
                                · <?= htmlspecialchars(Lang::t('mes_voyages.hotel', ['nom' => $hotel->getNom()])) ?>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="voyage-talon">
                        <!-- Le statut sert aussi de classe CSS pour la couleur de la pastille -->
                        <span class="statut statut-<?= htmlspecialchars($v->getStatut()) ?>">
                            <?= htmlspecialchars(Lang::t('statut.' . $v->getStatut())) ?>
                        </span>

                        <div class="voyage-actions">
                            <?php if ($v->getStatut() === 'en_preparation'): ?>
                                <a href="propositions.php?voyage=<?= (int) $v->getId() ?>" class="btn"><?= htmlspecialchars(Lang::t('mes_voyages.propositions')) ?></a>
                            <?php else: ?>
                                <a href="recapitulatif.php?voyage=<?= (int) $v->getId() ?>" class="btn"><?= htmlspecialchars(Lang::t('mes_voyages.voir')) ?></a>
                            <?php endif; ?>
                            <a href="voyage-modifier.php?voyage=<?= (int) $v->getId() ?>"><?= htmlspecialchars(Lang::t('mes_voyages.modifier')) ?></a>
                            <a href="voyage-supprimer.php?voyage=<?= (int) $v->getId() ?>" class="lien-danger"><?= htmlspecialchars(Lang::t('mes_voyages.supprimer')) ?></a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../src/partials/footer.php'; ?>
