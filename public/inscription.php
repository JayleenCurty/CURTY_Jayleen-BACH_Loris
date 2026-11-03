<?php
require_once __DIR__ . '/../src/autoload.php';

// Déjà connecté : pas besoin de créer un compte
if (Auth::estConnecte()) {
    header('Location: mes-voyages.php');
    exit;
}

$database           = new Database();
$utilisateurManager = new UtilisateurManager($database->getPdo());

// Valeurs du formulaire (vides au premier affichage)
$prenom = '';
$nom    = '';
$email  = '';

// Messages d'erreur, un par champ : ['email' => 'Adresse invalide', ...]
$erreurs = [];

// Le formulaire a été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Récupérer et nettoyer les saisies
    $prenom       = trim($_POST['prenom'] ?? '');
    $nom          = trim($_POST['nom'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $motDePasse   = $_POST['mot_de_passe'] ?? '';
    $confirmation = $_POST['confirmation'] ?? '';

    // 2. Valider côté serveur (on ne fait jamais confiance au navigateur)
    if ($prenom === '' || mb_strlen($prenom) > 50) {
        $erreurs['prenom'] = Lang::t('inscription.err_prenom');
    }

    if ($nom === '' || mb_strlen($nom) > 50) {
        $erreurs['nom'] = Lang::t('inscription.err_nom');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = Lang::t('inscription.err_email');
    } elseif ($utilisateurManager->emailExiste($email)) {
        $erreurs['email'] = Lang::t('inscription.err_email_pris');
    }

    if (mb_strlen($motDePasse) < 8) {
        $erreurs['mot_de_passe'] = Lang::t('inscription.err_mdp');
    }

    if ($motDePasse !== $confirmation) {
        $erreurs['confirmation'] = Lang::t('inscription.err_confirmation');
    }

    // 3. Tout est valide : on crée le compte et on redirige vers la connexion
    if (count($erreurs) === 0) {
        $utilisateurManager->creer($prenom, $nom, $email, $motDePasse);

        header('Location: connexion.php?inscription=ok');
        exit;
    }
}

$titrePage = Lang::t('inscription.titre_page');
$cssPage   = 'formulaires';
require __DIR__ . '/../src/partials/header.php';
?>

<main class="page">
    <section class="carte-formulaire">
        <h1><?= htmlspecialchars(Lang::t('inscription.titre')) ?></h1>
        <p class="sous-titre"><?= htmlspecialchars(Lang::t('inscription.sous_titre')) ?></p>

        <form method="post" action="inscription.php">
            <div class="ligne">
                <div class="champ">
                    <label for="prenom"><?= htmlspecialchars(Lang::t('inscription.prenom')) ?></label>
                    <input type="text" id="prenom" name="prenom" maxlength="50" required
                        value="<?= htmlspecialchars($prenom) ?>" />
                    <?php if (isset($erreurs['prenom'])): ?>
                        <p class="erreur"><?= htmlspecialchars($erreurs['prenom']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="champ">
                    <label for="nom"><?= htmlspecialchars(Lang::t('inscription.nom')) ?></label>
                    <input type="text" id="nom" name="nom" maxlength="50" required
                        value="<?= htmlspecialchars($nom) ?>" />
                    <?php if (isset($erreurs['nom'])): ?>
                        <p class="erreur"><?= htmlspecialchars($erreurs['nom']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="champ">
                <label for="email"><?= htmlspecialchars(Lang::t('inscription.email')) ?></label>
                <input type="email" id="email" name="email" required
                    value="<?= htmlspecialchars($email) ?>" />
                <?php if (isset($erreurs['email'])): ?>
                    <p class="erreur"><?= htmlspecialchars($erreurs['email']) ?></p>
                <?php endif; ?>
            </div>

            <div class="champ">
                <label for="mot_de_passe"><?= htmlspecialchars(Lang::t('inscription.mot_de_passe')) ?></label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" minlength="8" required />
                <p class="aide"><?= htmlspecialchars(Lang::t('inscription.aide_mdp')) ?></p>
                <?php if (isset($erreurs['mot_de_passe'])): ?>
                    <p class="erreur"><?= htmlspecialchars($erreurs['mot_de_passe']) ?></p>
                <?php endif; ?>
            </div>

            <div class="champ">
                <label for="confirmation"><?= htmlspecialchars(Lang::t('inscription.confirmation')) ?></label>
                <input type="password" id="confirmation" name="confirmation" minlength="8" required />
                <?php if (isset($erreurs['confirmation'])): ?>
                    <p class="erreur"><?= htmlspecialchars($erreurs['confirmation']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn"><?= htmlspecialchars(Lang::t('inscription.bouton')) ?></button>
        </form>

        <p class="bas-formulaire">
            <?= htmlspecialchars(Lang::t('inscription.deja_compte')) ?> <a href="connexion.php"><?= htmlspecialchars(Lang::t('inscription.se_connecter')) ?></a>
        </p>
    </section>
</main>


<?php require __DIR__ . '/../src/partials/footer.php'; ?>
