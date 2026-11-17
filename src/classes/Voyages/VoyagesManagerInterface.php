<?php
// Contrat du VoyagesManager : la liste des opérations possibles sur les voyages.
// La classe qui l'implémente DOIT contenir ces méthodes, avec exactement ces paramètres et types.

namespace Voyages;

interface VoyagesManagerInterface {
    // Tous les voyages d'un utilisateur
    public function getVoyagesUtilisateur(int $idUtilisateur): array;

    // Un voyage, seulement s'il appartient à cet utilisateur (sinon null)
    public function getVoyageUtilisateur(int $idVoyage, int $idUtilisateur): ?Voyage;

    // Enregistre un nouveau voyage et retourne son id
    public function addVoyage(Voyage $voyage): int;

    // Enregistre les modifications ; retourne true si le vol / l'hôtel choisis ont été annulés
    public function updateVoyage(Voyage $voyage): bool;

    // Supprime un voyage (et ses tâches) de cet utilisateur
    public function removeVoyage(int $idVoyage, int $idUtilisateur): bool;
}
