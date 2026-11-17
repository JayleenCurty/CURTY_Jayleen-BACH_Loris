<?php

namespace Taches;

use InvalidArgumentException;

// Une tâche de la checklist d'un voyage.
class Tache {
    const LIBELLE_MAX = 255;

    // Propriétés privées pour assurer l'encapsulation
    private ?int $id = null;
    private string $libelle = '';
    private bool $faite = false;
    private ?int $idVoyage = null;

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getLibelle(): string {
        return $this->libelle;
    }

    // Pour un booléen, on écrit souvent isXxx() plutôt que getXxx()
    public function isFaite(): bool {
        return $this->faite;
    }

    public function getIdVoyage(): ?int {
        return $this->idVoyage;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setLibelle(string $libelle): void {
        $libelle = trim($libelle);

        if ($libelle === '' || mb_strlen($libelle) > self::LIBELLE_MAX) {
            throw new InvalidArgumentException('Le libellé de la tâche est obligatoire (255 caractères maximum).');
        }

        $this->libelle = $libelle;
    }

    public function setFaite(bool $faite): void {
        $this->faite = $faite;
    }

    public function setIdVoyage(int $idVoyage): void {
        $this->idVoyage = $idVoyage;
    }
}
