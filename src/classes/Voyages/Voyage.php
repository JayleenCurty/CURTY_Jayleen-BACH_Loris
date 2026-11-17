<?php

namespace Voyages;

use DateTime;
use InvalidArgumentException;
use Lang;

class Voyage {
    // Valeurs autorisées : les mêmes que les ENUM de la table voyage (01-schema.sql)
    const TYPES_VOYAGE      = ['amis', 'evjf_evg', 'etudes', 'seminaire', 'autre'];
    const TYPES_DESTINATION = ['plage', 'ville', 'montagne', 'nature'];
    const STATUTS           = ['en_preparation', 'vol_choisi', 'hotel_choisi', 'pret'];

    // Limites du nombre de participant·es (le minimum correspond au CHECK de la base)
    const PERSONNES_MIN = 2;
    const PERSONNES_MAX = 30;
    const BUDGET_MAX    = 100000;

    // Propriétés privées pour assurer l'encapsulation
    private ?int $id = null;
    private string $nom = '';
    private string $typeVoyage = '';
    private int $nbrPersonne = 0;
    private float $budgetPersonne = 0.0;
    private ?DateTime $dateAller = null;
    private ?DateTime $dateRetour = null;
    private string $statut = 'en_preparation';
    private string $typeDestination = '';
    private ?int $idUtilisateur = null;
    private ?int $idVol = null;      // null tant que le vol n'est pas choisi
    private ?int $idHotel = null;    // null tant que l'hôtel n'est pas choisi

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getTypeVoyage(): string {
        return $this->typeVoyage;
    }

    public function getNbrPersonne(): int {
        return $this->nbrPersonne;
    }

    public function getBudgetPersonne(): float {
        return $this->budgetPersonne;
    }

    public function getDateAller(): ?DateTime {
        return $this->dateAller;
    }

    public function getDateRetour(): ?DateTime {
        return $this->dateRetour;
    }

    public function getStatut(): string {
        return $this->statut;
    }

    public function getTypeDestination(): string {
        return $this->typeDestination;
    }

    public function getIdUtilisateur(): ?int {
        return $this->idUtilisateur;
    }

    public function getIdVol(): ?int {
        return $this->idVol;
    }

    public function getIdHotel(): ?int {
        return $this->idHotel;
    }

    // Setters : chacun vérifie la valeur et lance une exception si elle n'est pas valide
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setNom(string $nom): void {
        $nom = trim($nom);

        if ($nom === '' || mb_strlen($nom) > 100) {
            throw new InvalidArgumentException(Lang::t('voyage.err_nom'));
        }

        $this->nom = $nom;
    }

    public function setTypeVoyage(string $typeVoyage): void {
        // La valeur doit faire partie de l'ENUM, sinon MariaDB refuserait l'enregistrement
        if (!in_array($typeVoyage, self::TYPES_VOYAGE, true)) {
            throw new InvalidArgumentException(Lang::t('voyage.err_type_voyage'));
        }

        $this->typeVoyage = $typeVoyage;
    }

    public function setNbrPersonne(int $nbrPersonne): void {
        if ($nbrPersonne < self::PERSONNES_MIN || $nbrPersonne > self::PERSONNES_MAX) {
            throw new InvalidArgumentException(Lang::t('voyage.err_personnes', [
                'min' => self::PERSONNES_MIN,
                'max' => self::PERSONNES_MAX,
            ]));
        }

        $this->nbrPersonne = $nbrPersonne;
    }

    public function setBudgetPersonne(float $budgetPersonne): void {
        if ($budgetPersonne <= 0 || $budgetPersonne > self::BUDGET_MAX) {
            throw new InvalidArgumentException(Lang::t('voyage.err_budget'));
        }

        $this->budgetPersonne = $budgetPersonne;
    }

    public function setDateAller(DateTime $dateAller): void {
        $this->dateAller = $dateAller;
    }

    // La date de retour doit être après la date de départ (si elle est déjà connue)
    public function setDateRetour(DateTime $dateRetour): void {
        if ($this->dateAller !== null && $dateRetour <= $this->dateAller) {
            throw new InvalidArgumentException(Lang::t('voyage.err_date_ordre'));
        }

        $this->dateRetour = $dateRetour;
    }

    public function setStatut(string $statut): void {
        if (!in_array($statut, self::STATUTS, true)) {
            throw new InvalidArgumentException('Statut invalide.');
        }

        $this->statut = $statut;
    }

    public function setTypeDestination(string $typeDestination): void {
        if (!in_array($typeDestination, self::TYPES_DESTINATION, true)) {
            throw new InvalidArgumentException(Lang::t('voyage.err_type_destination'));
        }

        $this->typeDestination = $typeDestination;
    }

    public function setIdUtilisateur(int $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }

    public function setIdVol(?int $idVol): void {
        $this->idVol = $idVol;
    }

    public function setIdHotel(?int $idHotel): void {
        $this->idHotel = $idHotel;
    }
}
