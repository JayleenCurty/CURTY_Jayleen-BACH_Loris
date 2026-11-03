<?php
require_once __DIR__ . '/../src/autoload.php';

// Déjà connecté : rien à faire ici
if (Auth::estConnecte()) {
    header('Location: mes-voyages.php');
    exit;
}

$database           = new Database();
$utilisateurManager = new UtilisateurManager($database->getPdo());

$email  = '';
$erreur = '';

// Message après une inscription réussie (inscription.php redirige vers connexion.php?inscription=ok)
$inscriptionReussie = ($_GET['inscription'] ?? '') === 'ok';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email      = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    $utilisateur = $utilisateurManager->trouverParEmail($email);

    // password_verify compare le mot de passe saisi avec le hash stocké en base
    if ($utilisateur !== null && password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
        Auth::connecter($utilisateur);

        header('Location: mes-voyages.php');
        exit;
    }

    // Même message que l'e-mail ou le mot de passe soit faux :
    // on ne révèle pas quelles adresses ont un compte
    $erreur = Lang::t('connexion.erreur');
}

$titrePage = Lang::t('connexion.titre_page');
$cssPage   = 'formulaires';
require __DIR__ . '/../src/partials/header.php';
?>

<main class="page">
    <section class="carte-formulaire">
        <h1><?= htmlspecialchars(Lang::t('connexion.titre')) ?></h1>
        <p class="sous-titre"><?= htmlspecialchars(Lang::t('connexion.sous_titre')) ?></p>

        <?php if ($inscriptionReussie): ?>
            <p class="succes"><?= htmlspecialchars(Lang::t('connexion.succes')) ?></p>
        <?php endif; ?>

        <?php if ($erreur !== ''): ?>
            <p class="erreur erreur-globale"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <form method="post" action="connexion.php">
            <div class="champ">
                <label for="email"><?= htmlspecialchars(Lang::t('connexion.email')) ?></label>
                <input type="email" id="email" name="email" required
                    value="<?= htmlspecialchars($email) ?>" />
            </div>

            <div class="champ">
                <label for="mot_de_passe"><?= htmlspecialchars(Lang::t('connexion.mot_de_passe')) ?></label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required />
            </div>

            <button type="submit" class="btn"><?= htmlspecialchars(Lang::t('connexion.bouton')) ?></button>
        </form>

        <p class="bas-formulaire">
            <?= htmlspecialchars(Lang::t('connexion.pas_compte')) ?> <a href="inscription.php"><?= htmlspecialchars(Lang::t('connexion.creer_compte')) ?></a>
        </p>
    </section>
</main>

<?php require __DIR__ . '/../src/partials/footer.php'; ?>
