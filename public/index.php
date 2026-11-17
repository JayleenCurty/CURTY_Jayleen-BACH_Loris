<?php
require_once __DIR__ . '/../src/utils/autoloader.php';

use Destinations\DestinationsManager;
use Hotels\HotelsManager;
use Vols\VolsManager;

// Création des Managers dont la page a besoin
$destinationsManager = new DestinationsManager();
$volsManager         = new VolsManager();
$hotelsManager       = new HotelsManager();

// Chiffres affichés sous le billet
$nbDestinations = $destinationsManager->compter();
$nbVols         = $volsManager->compter();
$nbHotels       = $hotelsManager->compter();

// Quatre destinations pour la section du bas (tableau d'objets Destination)
$destinations = $destinationsManager->getDestinations(4);

// Types de voyage : valeurs de l'ENUM type_voyage, traduites avec Lang::t('type_voyage.xxx')
$typesVoyage = ['amis', 'evjf_evg', 'etudes', 'seminaire', 'autre'];

$titrePage = Lang::t('accueil.titre_page');
$cssPage   = 'accueil';
require __DIR__ . '/../src/partials/header.php';
?>

<main class="page">
    <!-- Billet d'avion -->
    <section class="billet">
        <div class="billet-principal">
            <p class="trajet">GVA <span>✈</span> ???</p>
            <h1><?= htmlspecialchars(Lang::t('accueil.titre')) ?></h1>
            <div class="actions">
                <a href="inscription.php" class="btn"><?= htmlspecialchars(Lang::t('accueil.planifier')) ?></a>
                <a href="connexion.php" class="btn btn-secondaire"><?= htmlspecialchars(Lang::t('accueil.se_connecter')) ?></a>
            </div>
        </div>

        <div class="billet-talon">
            <p><?= htmlspecialchars(Lang::t('accueil.passagers')) ?><strong><?= htmlspecialchars(Lang::t('accueil.passagers_valeur')) ?></strong></p>
            <p><?= htmlspecialchars(Lang::t('accueil.depart')) ?><strong><?= htmlspecialchars(Lang::t('accueil.depart_valeur')) ?></strong></p>
            <p><?= htmlspecialchars(Lang::t('accueil.destination')) ?><strong><?= htmlspecialchars(Lang::t('accueil.destination_valeur')) ?></strong></p>
        </div>
    </section>

    <!-- Chiffres -->
    <section class="chiffres">
        <p><strong><?= $nbDestinations ?></strong> <?= htmlspecialchars(Lang::t('accueil.nb_destinations')) ?></p>
        <p><strong><?= $nbVols ?></strong> <?= htmlspecialchars(Lang::t('accueil.nb_vols')) ?></p>
        <p><strong><?= $nbHotels ?></strong> <?= htmlspecialchars(Lang::t('accueil.nb_hotels')) ?></p>
    </section>

    <!-- Comment ça marche -->
    <section class="etapes">
        <div class="etape">
            <span class="numero">01</span>
            <h2><?= htmlspecialchars(Lang::t('accueil.etape1_titre')) ?></h2>
            <p><?= htmlspecialchars(Lang::t('accueil.etape1_texte')) ?></p>
        </div>
        <div class="etape">
            <span class="numero">02</span>
            <h2><?= htmlspecialchars(Lang::t('accueil.etape2_titre')) ?></h2>
            <p><?= htmlspecialchars(Lang::t('accueil.etape2_texte')) ?></p>
        </div>
        <div class="etape">
            <span class="numero">03</span>
            <h2><?= htmlspecialchars(Lang::t('accueil.etape3_titre')) ?></h2>
            <p><?= htmlspecialchars(Lang::t('accueil.etape3_texte')) ?></p>
        </div>
    </section>

    <!-- Types de voyage + destinations -->
    <section class="evenements">
        <div>
            <h2><?= htmlspecialchars(Lang::t('accueil.evenement')) ?></h2>
            <ul class="types">
                <?php foreach ($typesVoyage as $type): ?>
                    <li><?= htmlspecialchars(Lang::t('type_voyage.' . $type)) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="destinations">
            <?php foreach ($destinations as $d): ?>
                <article class="destination">
                    <span class="destination-type"><?= htmlspecialchars(Lang::t('type_destination.' . $d->getTypeDestination())) ?></span>
                    <h3><?= htmlspecialchars(Lang::choisir($d->getVille(), $d->getVilleEn())) ?></h3>
                    <p><?= htmlspecialchars(Lang::choisir($d->getPays(), $d->getPaysEn())) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>


<?php require __DIR__ . '/../src/partials/footer.php'; ?>
