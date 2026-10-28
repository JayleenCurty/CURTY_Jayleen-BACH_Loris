<?php
require_once __DIR__ . '/../src/autoload.php';

// Connexion à la base et création des objets dont la page a besoin
$database           = new Database();
$destinationManager = new DestinationManager($database->getPdo());
$volManager         = new VolManager($database->getPdo());
$hotelManager       = new HotelManager($database->getPdo());

// Chiffres affichés sous le billet
$nbDestinations = $destinationManager->compter();
$nbVols         = $volManager->compter();
$nbHotels       = $hotelManager->compter();

// Quatre destinations pour la section du bas
$destinations = $destinationManager->getQuelquesDestinations(4);

// Types de voyage (correspondent à l'ENUM type_voyage de la table voyage)
$typesVoyage = [
    'Vacances entre ami·es',
    'EVJF / EVG',
    "Voyages d'études",
    "Séminaires d'entreprise",
    "Sorties d'associations",
];

$titrePage = 'Accueil';
$cssPage   = 'accueil';
require __DIR__ . '/../src/partials/header.php';
?>

<main class="page">
    <!-- Billet d'avion -->
    <section class="billet">
        <div class="billet-principal">
            <p class="trajet">GVA <span>✈</span> ???</p>
            <h1>Organisez votre voyage de groupe</h1>
            <div class="actions">
                <a href="inscription.php" class="btn">Planifier un voyage</a>
                <a href="connexion.php" class="btn btn-secondaire">Se connecter</a>
            </div>
        </div>

        <div class="billet-talon">
            <p>Passagers<strong>2 à 30</strong></p>
            <p>Départ<strong>Genève Aéroport</strong></p>
            <p>Destination<strong>À vous de choisir</strong></p>
        </div>
    </section>

    <!-- Chiffres -->
    <section class="chiffres">
        <p><strong><?= $nbDestinations ?></strong> destinations</p>
        <p><strong><?= $nbVols ?></strong> vols</p>
        <p><strong><?= $nbHotels ?></strong> hôtels</p>
    </section>

    <!-- Comment ça marche -->
    <section class="etapes">
        <div class="etape">
            <span class="numero">01</span>
            <h2>Vos critères</h2>
            <p>Nombre de personnes, budget et dates.</p>
        </div>
        <div class="etape">
            <span class="numero">02</span>
            <h2>Une proposition</h2>
            <p>Un vol et un hôtel pour tout le groupe.</p>
        </div>
        <div class="etape">
            <span class="numero">03</span>
            <h2>Le départ</h2>
            <p>Une liste de tâches à cocher avant de partir.</p>
        </div>
    </section>

    <!-- Types de voyage + destinations -->
    <section class="evenements">
        <div>
            <h2>Quel que soit l'événement</h2>
            <ul class="types">
                <?php foreach ($typesVoyage as $type): ?>
                    <li><?= htmlspecialchars($type) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="destinations">
            <?php foreach ($destinations as $d): ?>
                <article class="destination">
                    <span class="destination-type"><?= htmlspecialchars($d['type_destination']) ?></span>
                    <h3><?= htmlspecialchars($d['ville']) ?></h3>
                    <p><?= htmlspecialchars($d['pays']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>


<?php require __DIR__ . '/../src/partials/footer.php'; ?>
