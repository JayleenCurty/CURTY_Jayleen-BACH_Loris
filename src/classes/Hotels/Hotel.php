<?php

namespace Hotels;

// Un hôtel d'une destination.
class Hotel {
    // Propriétés privées pour assurer l'encapsulation
    private ?int $id = null;
    private string $nom = '';
    private ?int $etoiles = null;
    private float $prixNuit = 0.0;
    private int $capacite = 0;
    private int $idDestination = 0;

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getEtoiles(): ?int {
        return $this->etoiles;
    }

    public function getPrixNuit(): float {
        return $this->prixNuit;
    }

    public function getCapacite(): int {
        return $this->capacite;
    }

    public function getIdDestination(): int {
        return $this->idDestination;
    }

    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function setEtoiles(?int $etoiles): void {
        $this->etoiles = $etoiles;
    }

    public function setPrixNuit(float $prixNuit): void {
        $this->prixNuit = $prixNuit;
    }

    public function setCapacite(int $capacite): void {
        $this->capacite = $capacite;
    }

    public function setIdDestination(int $idDestination): void {
        $this->idDestination = $idDestination;
    }
}
