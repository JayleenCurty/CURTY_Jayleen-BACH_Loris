<?php
require_once __DIR__ . '/../src/autoload.php';

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
        $erreurs['prenom'] = 'Le prénom est obligatoire (50 caractères maximum).';
    }

    if ($nom === '' || mb_strlen($nom) > 50) {
        $erreurs['nom'] = 'Le nom est obligatoire (50 caractères maximum).';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = "L'adresse e-mail n'est pas valide.";
    } elseif ($utilisateurManager->emailExiste($email)) {
        $erreurs['email'] = 'Un compte existe déjà avec cette adresse.';
    }

    if (mb_strlen($motDePasse) < 8) {
        $erreurs['mot_de_passe'] = 'Le mot de passe doit contenir au moins 8 caractères.';
    }

    if ($motDePasse !== $confirmation) {
        $erreurs['confirmation'] = 'Les deux mots de passe ne correspondent pas.';
    }

    // 3. Tout est valide : on crée le compte et on redirige vers la connexion
    if (count($erreurs) === 0) {
        $utilisateurManager->creer($prenom, $nom, $email, $motDePasse);

        header('Location: connexion.php?inscription=ok');
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inscription - GroupTrip</title>
    <link rel="stylesheet" href="./css/reset.css" />
    <link rel="stylesheet" href="./css/base.css" />
    <link rel="stylesheet" href="./css/formulaires.css" />
</head>

<body>
    <header class="site-header">
        <a href="index.php" class="logo">GroupTrip</a>
        <nav>
            <a href="connexion.php">Connexion</a>
            <a href="inscription.php">Inscription</a>
        </nav>
    </header>

    <main class="page">
        <section class="carte-formulaire">
            <h1>Créer un compte</h1>
            <p class="sous-titre">Pour organiser et retrouver vos voyages.</p>

            <form method="post" action="inscription.php">
                <div class="ligne">
                    <div class="champ">
                        <label for="prenom">Prénom</label>
                        <input type="text" id="prenom" name="prenom" maxlength="50" required
                            value="<?= htmlspecialchars($prenom) ?>" />
                        <?php if (isset($erreurs['prenom'])): ?>
                            <p class="erreur"><?= htmlspecialchars($erreurs['prenom']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="champ">
                        <label for="nom">Nom</label>
                        <input type="text" id="nom" name="nom" maxlength="50" required
                            value="<?= htmlspecialchars($nom) ?>" />
                        <?php if (isset($erreurs['nom'])): ?>
                            <p class="erreur"><?= htmlspecialchars($erreurs['nom']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="champ">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required
                        value="<?= htmlspecialchars($email) ?>" />
                    <?php if (isset($erreurs['email'])): ?>
                        <p class="erreur"><?= htmlspecialchars($erreurs['email']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="champ">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" minlength="8" required />
                    <p class="aide">8 caractères minimum.</p>
                    <?php if (isset($erreurs['mot_de_passe'])): ?>
                        <p class="erreur"><?= htmlspecialchars($erreurs['mot_de_passe']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="champ">
                    <label for="confirmation">Confirmer le mot de passe</label>
                    <input type="password" id="confirmation" name="confirmation" minlength="8" required />
                    <?php if (isset($erreurs['confirmation'])): ?>
                        <p class="erreur"><?= htmlspecialchars($erreurs['confirmation']) ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn">Créer mon compte</button>
            </form>

            <p class="bas-formulaire">
                Déjà un compte ? <a href="connexion.php">Se connecter</a>
            </p>
        </section>
    </main>

    <footer class="site-footer">
        Projet ProgServ2 – HEIG-VD
    </footer>
</body>

</html>
