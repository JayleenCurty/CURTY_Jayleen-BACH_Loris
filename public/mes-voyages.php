<?php
require_once __DIR__ . '/../src/autoload.php';

// Page privée : on renvoie vers la connexion si personne n'est connecté
Auth::exigerConnexion();

$utilisateur = Auth::utilisateur();

$database      = new Database();
$voyageManager = new VoyageManager($database->getPdo());

// Uniquement les voyages de la personne connectée
$voyages = $voyageManager->listerPourUtilisateur($utilisateur['id']);

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
                <article class="voyage">
                    <div class="voyage-principal">
                        <p class="voyage-trajet">
                            GVA <span>✈</span>
                            <?php if ($v['ville'] !== null): ?>
                                <?= htmlspecialchars(Lang::champ($v, 'ville')) ?>
                            <?php else: ?>
                                ???
                            <?php endif; ?>
                        </p>

                        <h2><?= htmlspecialchars($v['nom']) ?></h2>

                        <p class="voyage-infos">
                            <?= htmlspecialchars(Lang::date($v['date_aller'])) ?> – <?= htmlspecialchars(Lang::date($v['date_retour'])) ?>
                            · <?= htmlspecialchars(Lang::t('mes_voyages.personnes', ['nombre' => $v['nbr_personne']])) ?>
                            · <?= htmlspecialchars(Lang::t('mes_voyages.budget', ['montant' => number_format((float) $v['budget_personne'], 0, '.', "'")])) ?>
                        </p>

                        <p class="voyage-infos">
                            <?= htmlspecialchars(Lang::t('type_voyage.' . $v['type_voyage'])) ?>
                            · <?= htmlspecialchars(ucfirst(Lang::t('type_destination.' . $v['type_destination']))) ?>
                            <?php if ($v['hotel_nom'] !== null): ?>
                                · <?= htmlspecialchars(Lang::t('mes_voyages.hotel', ['nom' => $v['hotel_nom']])) ?>
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="voyage-talon">
                        <!-- Le statut sert aussi de classe CSS pour la couleur de la pastille -->
                        <span class="statut statut-<?= htmlspecialchars($v['statut']) ?>">
                            <?= htmlspecialchars(Lang::t('statut.' . $v['statut'])) ?>
                        </span>

                        <div class="voyage-actions">
                            <?php if ($v['statut'] === 'en_preparation'): ?>
                                <a href="propositions.php?voyage=<?= (int) $v['id'] ?>" class="btn"><?= htmlspecialchars(Lang::t('mes_voyages.propositions')) ?></a>
                            <?php else: ?>
                                <a href="recapitulatif.php?voyage=<?= (int) $v['id'] ?>" class="btn"><?= htmlspecialchars(Lang::t('mes_voyages.voir')) ?></a>
                            <?php endif; ?>
                            <a href="voyage-modifier.php?voyage=<?= (int) $v['id'] ?>"><?= htmlspecialchars(Lang::t('mes_voyages.modifier')) ?></a>
                            <a href="voyage-supprimer.php?voyage=<?= (int) $v['id'] ?>" class="lien-danger"><?= htmlspecialchars(Lang::t('mes_voyages.supprimer')) ?></a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../src/partials/footer.php'; ?>
