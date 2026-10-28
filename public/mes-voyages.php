<?php
require_once __DIR__ . '/../src/autoload.php';

// Page privée : on renvoie vers la connexion si personne n'est connecté
Auth::exigerConnexion();

$utilisateur = Auth::utilisateur();

$titrePage = 'Mes voyages';
$cssPage   = 'formulaires';
require __DIR__ . '/../src/partials/header.php';
?>

<main class="page">
    <h1>Mes voyages</h1>
    <p>Connecté en tant que <?= htmlspecialchars($utilisateur['prenom']) ?>. Cette page sera construite à la prochaine étape.</p>
</main>

<?php require __DIR__ . '/../src/partials/footer.php'; ?>
