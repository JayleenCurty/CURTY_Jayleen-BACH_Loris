<?php

namespace Vols;

// Un vol aller-retour au départ de Genève.
class Vol {
    // Propriétés privées pour assurer l'encapsulation
    private ?int $id = null;
    private string $compagnie = '';
    private ?\DateTime $dateAller = null;
    private ?\DateTime $dateRetour = null;
    private int $dureeVol = 0;
    private float $prixPersonne = 0.0;
    private int $placesDisponibles = 0;
    private int $idDestination = 0;

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getCompagnie(): string {
        return $this->compagnie;
    }

    public function getDateAller(): ?\DateTime {
        return $this->dateAller;
    }

    public function getDateRetour(): ?\DateTime {
        return $this->dateRetour;
    }

    public function getDureeVol(): int {
        return $this->dureeVol;
    }

    public function getPrixPersonne(): float {
        return $this->prixPersonne;
    }

    public function getPlacesDisponibles(): int {
        return $this->placesDisponibles;
    }

    public function getIdDestination(): int {
        return $this->idDestination;
    }

    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setCompagnie(string $compagnie): void {
        $this->compagnie = $compagnie;
    }

    public function setDateAller(?\DateTime $dateAller): void {
        $this->dateAller = $dateAller;
    }

    public function setDateRetour(?\DateTime $dateRetour): void {
        $this->dateRetour = $dateRetour;
    }

    public function setDureeVol(int $dureeVol): void {
        $this->dureeVol = $dureeVol;
    }

    public function setPrixPersonne(float $prixPersonne): void {
        $this->prixPersonne = $prixPersonne;
    }

    public function setPlacesDisponibles(int $placesDisponibles): void {
        $this->placesDisponibles = $placesDisponibles;
    }

    public function setIdDestination(int $idDestination): void {
        $this->idDestination = $idDestination;
    }
}
