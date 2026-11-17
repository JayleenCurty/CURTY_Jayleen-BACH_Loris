<?php
require_once __DIR__ . '/../src/utils/autoloader.php';

use Utilisateurs\Utilisateur;
use Utilisateurs\UtilisateursManager;

// Déjà connecté : pas besoin de créer un compte
if (Auth::estConnecte()) {
    header('Location: mes-voyages.php');
    exit;
}

$utilisateursManager = new UtilisateursManager();

// Valeurs du formulaire (vides au premier affichage)
$prenom = '';
$nom    = '';
$email  = '';

// Messages d'erreur, un par champ : ['email' => 'Adresse invalide', ...]
$erreurs = [];

// Le formulaire a été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Récupérer les saisies
    $prenom       = trim($_POST['prenom'] ?? '');
    $nom          = trim($_POST['nom'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $motDePasse   = $_POST['mot_de_passe'] ?? '';
    $confirmation = $_POST['confirmation'] ?? '';

    // 2. Remplir un objet Utilisateur : chaque setter vérifie sa valeur
    //    et lance une InvalidArgumentException si elle n'est pas valide
    $utilisateur = new Utilisateur();

    try {
        $utilisateur->setPrenom($prenom);
    } catch (InvalidArgumentException $e) {
        $erreurs['prenom'] = $e->getMessage();
    }

    try {
        $utilisateur->setNom($nom);
    } catch (InvalidArgumentException $e) {
        $erreurs['nom'] = $e->getMessage();
    }

    try {
        $utilisateur->setEmail($email);

        // Règle qui dépend de la base : c'est le Manager qui la vérifie
        if ($utilisateursManager->emailExiste($email)) {
            $erreurs['email'] = Lang::t('inscription.err_email_pris');
        }
    } catch (InvalidArgumentException $e) {
        $erreurs['email'] = $e->getMessage();
    }

    try {
        // Vérifie la longueur puis hache le mot de passe (voir Utilisateur::changerMotDePasse)
        $utilisateur->changerMotDePasse($motDePasse);
    } catch (InvalidArgumentException $e) {
        $erreurs['mot_de_passe'] = $e->getMessage();
    }

    if ($motDePasse !== $confirmation) {
        $erreurs['confirmation'] = Lang::t('inscription.err_confirmation');
    }

    // 3. Tout est valide : on enregistre le compte et on redirige vers la connexion
    if (count($erreurs) === 0) {
        $utilisateursManager->addUtilisateur($utilisateur);

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
