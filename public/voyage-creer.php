<?php
require_once __DIR__ . '/../src/autoload.php';

// Page privée
Auth::exigerConnexion();

$database       = new Database();
$voyageManager  = new VoyageManager($database->getPdo());

// Valeurs du formulaire (vides au premier affichage)
$voyage = [
    'nom'              => '',
    'type_voyage'      => '',
    'nbr_personne'     => '',
    'budget_personne'  => '',
    'date_aller'       => '',
    'date_retour'      => '',
    'type_destination' => '',
];
$erreurs = [];

// Date du jour au format AAAA-MM-JJ (même format que <input type="date"> et MariaDB)
$aujourdhui = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Récupérer et nettoyer les saisies
    foreach ($voyage as $champ => $valeur) {
        $voyage[$champ] = trim($_POST[$champ] ?? '');
    }

    // 2. Valider côté serveur
    if ($voyage['nom'] === '' || mb_strlen($voyage['nom']) > 100) {
        $erreurs['nom'] = Lang::t('voyage.err_nom');
    }

    // La valeur doit faire partie de la liste de l'ENUM (sinon MariaDB refuserait l'INSERT)
    if (!in_array($voyage['type_voyage'], VoyageManager::TYPES_VOYAGE, true)) {
        $erreurs['type_voyage'] = Lang::t('voyage.err_type_voyage');
    }

    if (!in_array($voyage['type_destination'], VoyageManager::TYPES_DESTINATION, true)) {
        $erreurs['type_destination'] = Lang::t('voyage.err_type_destination');
    }

    // filter_var vérifie que c'est bien un entier compris entre min et max
    $nbPersonnes = filter_var($voyage['nbr_personne'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => VoyageManager::PERSONNES_MIN, 'max_range' => VoyageManager::PERSONNES_MAX],
    ]);
    if ($nbPersonnes === false) {
        $erreurs['nbr_personne'] = Lang::t('voyage.err_personnes', [
            'min' => VoyageManager::PERSONNES_MIN,
            'max' => VoyageManager::PERSONNES_MAX,
        ]);
    }

    $budget = filter_var($voyage['budget_personne'], FILTER_VALIDATE_FLOAT);
    if ($budget === false || $budget <= 0 || $budget > 100000) {
        $erreurs['budget_personne'] = Lang::t('voyage.err_budget');
    }

    // Une date valide au format AAAA-MM-JJ, à partir d'aujourd'hui
    $dateAller = DateTime::createFromFormat('Y-m-d', $voyage['date_aller']);
    if ($dateAller === false || $dateAller->format('Y-m-d') !== $voyage['date_aller'] || $voyage['date_aller'] < $aujourdhui) {
        $erreurs['date_aller'] = Lang::t('voyage.err_date_aller');
    }

    $dateRetour = DateTime::createFromFormat('Y-m-d', $voyage['date_retour']);
    if ($dateRetour === false || $dateRetour->format('Y-m-d') !== $voyage['date_retour']) {
        $erreurs['date_retour'] = Lang::t('voyage.err_date_retour');
    } elseif (!isset($erreurs['date_aller']) && $voyage['date_retour'] <= $voyage['date_aller']) {
        $erreurs['date_retour'] = Lang::t('voyage.err_date_ordre');
    }

    // 3. Tout est valide : on enregistre et on redirige
    if (count($erreurs) === 0) {
        $voyage['nbr_personne']    = $nbPersonnes;
        $voyage['budget_personne'] = $budget;

        $idVoyage = $voyageManager->creer(Auth::utilisateur()['id'], $voyage);

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

        <form method="post" action="voyage-creer.php">
            <div class="champ">
                <label for="nom"><?= htmlspecialchars(Lang::t('voyage.nom')) ?></label>
                <input type="text" id="nom" name="nom" maxlength="100" required
                    placeholder="<?= htmlspecialchars(Lang::t('voyage.nom_exemple')) ?>"
                    value="<?= htmlspecialchars($voyage['nom']) ?>" />
                <?php if (isset($erreurs['nom'])): ?>
                    <p class="erreur"><?= htmlspecialchars($erreurs['nom']) ?></p>
                <?php endif; ?>
            </div>

            <div class="ligne">
                <div class="champ">
                    <label for="type_voyage"><?= htmlspecialchars(Lang::t('voyage.type_voyage')) ?></label>
                    <select id="type_voyage" name="type_voyage" required>
                        <option value=""><?= htmlspecialchars(Lang::t('voyage.choisir')) ?></option>
                        <?php foreach (VoyageManager::TYPES_VOYAGE as $type): ?>
                            <option value="<?= $type ?>" <?= $voyage['type_voyage'] === $type ? 'selected' : '' ?>>
                                <?= htmlspecialchars(Lang::t('type_voyage.' . $type)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($erreurs['type_voyage'])): ?>
                        <p class="erreur"><?= htmlspecialchars($erreurs['type_voyage']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="champ">
                    <label for="type_destination"><?= htmlspecialchars(Lang::t('voyage.type_destination')) ?></label>
                    <select id="type_destination" name="type_destination" required>
                        <option value=""><?= htmlspecialchars(Lang::t('voyage.choisir')) ?></option>
                        <?php foreach (VoyageManager::TYPES_DESTINATION as $type): ?>
                            <option value="<?= $type ?>" <?= $voyage['type_destination'] === $type ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst(Lang::t('type_destination.' . $type))) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($erreurs['type_destination'])): ?>
                        <p class="erreur"><?= htmlspecialchars($erreurs['type_destination']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ligne">
                <div class="champ">
                    <label for="nbr_personne"><?= htmlspecialchars(Lang::t('voyage.personnes')) ?></label>
                    <input type="number" id="nbr_personne" name="nbr_personne" required
                        min="<?= VoyageManager::PERSONNES_MIN ?>" max="<?= VoyageManager::PERSONNES_MAX ?>" step="1"
                        value="<?= htmlspecialchars($voyage['nbr_personne']) ?>" />
                    <?php if (isset($erreurs['nbr_personne'])): ?>
                        <p class="erreur"><?= htmlspecialchars($erreurs['nbr_personne']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="champ">
                    <label for="budget_personne"><?= htmlspecialchars(Lang::t('voyage.budget')) ?></label>
                    <input type="number" id="budget_personne" name="budget_personne" required
                        min="1" max="100000" step="1"
                        value="<?= htmlspecialchars($voyage['budget_personne']) ?>" />
                    <p class="aide"><?= htmlspecialchars(Lang::t('voyage.budget_aide')) ?></p>
                    <?php if (isset($erreurs['budget_personne'])): ?>
                        <p class="erreur"><?= htmlspecialchars($erreurs['budget_personne']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ligne">
                <div class="champ">
                    <label for="date_aller"><?= htmlspecialchars(Lang::t('voyage.date_aller')) ?></label>
                    <input type="date" id="date_aller" name="date_aller" required min="<?= $aujourdhui ?>"
                        value="<?= htmlspecialchars($voyage['date_aller']) ?>" />
                    <?php if (isset($erreurs['date_aller'])): ?>
                        <p class="erreur"><?= htmlspecialchars($erreurs['date_aller']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="champ">
                    <label for="date_retour"><?= htmlspecialchars(Lang::t('voyage.date_retour')) ?></label>
                    <input type="date" id="date_retour" name="date_retour" required min="<?= $aujourdhui ?>"
                        value="<?= htmlspecialchars($voyage['date_retour']) ?>" />
                    <?php if (isset($erreurs['date_retour'])): ?>
                        <p class="erreur"><?= htmlspecialchars($erreurs['date_retour']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn"><?= htmlspecialchars(Lang::t('voyage.bouton')) ?></button>
        </form>
    </section>
</main>

<?php require __DIR__ . '/../src/partials/footer.php'; ?>
