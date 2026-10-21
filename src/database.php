<?php
// Connexion à la base de données à partir du fichier de configuration
$config = parse_ini_file(__DIR__ . '/../config/database.ini');

if ($config === false) {
    die('Fichier de configuration introuvable : copiez config/database.example.ini en config/database.ini');
}

$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";

$pdo = new PDO($dsn, $config['username'], $config['password'], [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
