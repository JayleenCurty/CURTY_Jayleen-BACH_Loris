<?php

namespace Destinations;

// Une destination du catalogue (lecture seule pour l'instant : modifiée par l'admin plus tard).
class Destination {
    // Propriétés privées pour assurer l'encapsulation
    private ?int $id = null;
    private string $pays = '';
    private ?string $paysEn = null;
    private string $ville = '';
    private ?string $villeEn = null;
    private string $codeAeroport = '';
    private ?string $description = null;
    private ?string $descriptionEn = null;
    private ?string $image = null;
    private string $typeDestination = '';

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getPays(): string {
        return $this->pays;
    }

    public function getPaysEn(): ?string {
        return $this->paysEn;
    }

    public function getVille(): string {
        return $this->ville;
    }

    public function getVilleEn(): ?string {
        return $this->villeEn;
    }

    public function getCodeAeroport(): string {
        return $this->codeAeroport;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getDescriptionEn(): ?string {
        return $this->descriptionEn;
    }

    public function getImage(): ?string {
        return $this->image;
    }

    public function getTypeDestination(): string {
        return $this->typeDestination;
    }

    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setPays(string $pays): void {
        $this->pays = $pays;
    }

    public function setPaysEn(?string $paysEn): void {
        $this->paysEn = $paysEn;
    }

    public function setVille(string $ville): void {
        $this->ville = $ville;
    }

    public function setVilleEn(?string $villeEn): void {
        $this->villeEn = $villeEn;
    }

    public function setCodeAeroport(string $codeAeroport): void {
        $this->codeAeroport = $codeAeroport;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function setDescriptionEn(?string $descriptionEn): void {
        $this->descriptionEn = $descriptionEn;
    }

    public function setImage(?string $image): void {
        $this->image = $image;
    }

    public function setTypeDestination(string $typeDestination): void {
        $this->typeDestination = $typeDestination;
    }
}
