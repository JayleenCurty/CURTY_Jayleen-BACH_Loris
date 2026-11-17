<?php
// Contrat commun aux Managers qui savent compter les lignes de leur table.
// Utilisé par DestinationsManager, VolsManager et HotelsManager (chiffres de la page d'accueil).
// Une même interface peut être implémentée par plusieurs classes différentes :
// on sait alors que chacune possède une méthode compter(), quelle que soit sa table.

interface ComptableInterface {
    public function compter(): int;
}
