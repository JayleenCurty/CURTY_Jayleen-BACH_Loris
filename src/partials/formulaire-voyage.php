<?php

use Voyages\Voyage;

// Formulaire de voyage, utilisé par voyage-creer.php et voyage-modifier.php.
// La page doit définir avant de l'inclure :
//   $saisie           : les valeurs affichées dans les champs (texte), par nom de champ
//   $erreurs          : les messages d'erreur par champ
//   $actionFormulaire : la page qui reçoit le formulaire
//   $texteBouton      : le texte du bouton
//   $idVoyage         : (seulement en modification) l'id du voyage
$saisie           = $saisie ?? [];
$erreurs          = $erreurs ?? [];
$actionFormulaire = $actionFormulaire ?? 'voyage-creer.php';
$texteBouton      = $texteBouton ?? '';
?>
<form method="post" action="<?= htmlspecialchars($actionFormulaire) ?>">
    <?php if (isset($idVoyage)): ?>
        <!-- En modification : quel voyage on modifie -->
        <input type="hidden" name="voyage" value="<?= htmlspecialchars((string) (int) $idVoyage) ?>" /> <?php endif; ?>

    <div class="champ">
        <label for="nom"><?= htmlspecialchars(Lang::t('voyage.nom')) ?></label>
        <input type="text" id="nom" name="nom" maxlength="100" required
            placeholder="<?= htmlspecialchars(Lang::t('voyage.nom_exemple')) ?>"
            value="<?= htmlspecialchars(($saisie['nom'] ?? '')) ?>" />
        <?php if (isset($erreurs['nom'])): ?>
            <p class="erreur"><?= htmlspecialchars($erreurs['nom']) ?></p>
        <?php endif; ?>
    </div>

    <div class="ligne">
        <div class="champ">
            <label for="type_voyage"><?= htmlspecialchars(Lang::t('voyage.type_voyage')) ?></label>
            <select id="type_voyage" name="type_voyage" required>
                <option value=""><?= htmlspecialchars(Lang::t('voyage.choisir')) ?></option>
                <?php foreach (Voyage::TYPES_VOYAGE as $type): ?>
                    <option value="<?= htmlspecialchars($type) ?>" <?= ($saisie['type_voyage'] ?? '') === $type ? 'selected' : '' ?>>
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
                <?php foreach (Voyage::TYPES_DESTINATION as $type): ?>
                    <option value="<?= htmlspecialchars($type) ?>" <?= ($saisie['type_destination'] ?? '') === $type ? 'selected' : '' ?>>
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
                min="<?= htmlspecialchars((string) Voyage::PERSONNES_MIN) ?>" max="<?= htmlspecialchars((string) Voyage::PERSONNES_MAX) ?>" step="1" value="<?= htmlspecialchars(($saisie['nbr_personne'] ?? '')) ?>" />
            <?php if (isset($erreurs['nbr_personne'])): ?>
                <p class="erreur"><?= htmlspecialchars($erreurs['nbr_personne']) ?></p>
            <?php endif; ?>
        </div>

        <div class="champ">
            <label for="budget_personne"><?= htmlspecialchars(Lang::t('voyage.budget')) ?></label>
            <input type="number" id="budget_personne" name="budget_personne" required
                min="1" max="100000" step="1"
                value="<?= htmlspecialchars(($saisie['budget_personne'] ?? '')) ?>" />
            <p class="aide"><?= htmlspecialchars(Lang::t('voyage.budget_aide')) ?></p>
            <?php if (isset($erreurs['budget_personne'])): ?>
                <p class="erreur"><?= htmlspecialchars($erreurs['budget_personne']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="ligne">
        <div class="champ">
            <label for="date_aller"><?= htmlspecialchars(Lang::t('voyage.date_aller')) ?></label>
            <input type="date" id="date_aller" name="date_aller" required min="<?= htmlspecialchars(date('Y-m-d')) ?>"
                value="<?= htmlspecialchars(($saisie['date_aller'] ?? '')) ?>" />
            <?php if (isset($erreurs['date_aller'])): ?>
                <p class="erreur"><?= htmlspecialchars($erreurs['date_aller']) ?></p>
            <?php endif; ?>
        </div>

        <div class="champ">
            <label for="date_retour"><?= htmlspecialchars(Lang::t('voyage.date_retour')) ?></label>
            <input type="date" id="date_retour" name="date_retour" required min="<?= htmlspecialchars(date('Y-m-d')) ?>"
                value="<?= htmlspecialchars(($saisie['date_retour'] ?? '')) ?>" />
            <?php if (isset($erreurs['date_retour'])): ?>
                <p class="erreur"><?= htmlspecialchars($erreurs['date_retour']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <button type="submit" class="btn"><?= htmlspecialchars($texteBouton) ?></button>
</form>
