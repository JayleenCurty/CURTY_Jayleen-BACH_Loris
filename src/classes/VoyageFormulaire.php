<?php
// Validation du formulaire de voyage, partagée par voyage-creer.php et voyage-modifier.php.
// Une seule version des règles : si on en change une, les deux pages suivent.

class VoyageFormulaire {
    // Champs du formulaire, vides (premier affichage de la création)
    public const CHAMPS_VIDES = [
        'nom'              => '',
        'type_voyage'      => '',
        'nbr_personne'     => '',
        'budget_personne'  => '',
        'date_aller'       => '',
        'date_retour'      => '',
        'type_destination' => '',
    ];

    // Récupère et nettoie les champs envoyés par le formulaire ($_POST)
    public static function lire(array $post): array {
        $voyage = [];

        foreach (self::CHAMPS_VIDES as $champ => $vide) {
            $voyage[$champ] = trim($post[$champ] ?? '');
        }

        return $voyage;
    }

    // Vérifie les champs et retourne les erreurs : ['nom' => 'message', ...]
    // Tableau vide = tout est valide.
    public static function valider(array $voyage): array {
        $erreurs    = [];
        $aujourdhui = date('Y-m-d');

        if ($voyage['nom'] === '' || mb_strlen($voyage['nom']) > 100) {
            $erreurs['nom'] = Lang::t('voyage.err_nom');
        }

        // La valeur doit faire partie de la liste de l'ENUM (sinon MariaDB refuserait la requête)
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

        return $erreurs;
    }

    // Convertit les champs texte en vrais nombres avant l'enregistrement en base
    public static function preparer(array $voyage): array {
        $voyage['nbr_personne']    = (int) $voyage['nbr_personne'];
        $voyage['budget_personne'] = (float) $voyage['budget_personne'];

        return $voyage;
    }
}
