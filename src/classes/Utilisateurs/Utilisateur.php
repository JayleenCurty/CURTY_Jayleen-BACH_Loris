<?php

namespace Utilisateurs;

use InvalidArgumentException;
use Lang;

class Utilisateur {
    const ROLES = ['user', 'admin'];
    const MOT_DE_PASSE_MIN = 8;

    // Propriétés privées pour assurer l'encapsulation
    private ?int $id = null;
    private string $prenom = '';
    private string $nom = '';
    private string $email = '';
    private string $motDePasse = '';   // toujours le hash, jamais le mot de passe en clair
    private string $role = 'user';

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getPrenom(): string {
        return $this->prenom;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getMotDePasse(): string {
        return $this->motDePasse;
    }

    public function getRole(): string {
        return $this->role;
    }

    // Setters : chacun vérifie la valeur et lance une exception si elle n'est pas valide
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setPrenom(string $prenom): void {
        $prenom = trim($prenom);

        if ($prenom === '' || mb_strlen($prenom) > 50) {
            throw new InvalidArgumentException(Lang::t('inscription.err_prenom'));
        }

        $this->prenom = $prenom;
    }

    public function setNom(string $nom): void {
        $nom = trim($nom);

        if ($nom === '' || mb_strlen($nom) > 50) {
            throw new InvalidArgumentException(Lang::t('inscription.err_nom'));
        }

        $this->nom = $nom;
    }

    public function setEmail(string $email): void {
        $email = trim($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(Lang::t('inscription.err_email'));
        }

        $this->email = $email;
    }

    // Reçoit un mot de passe DÉJÀ haché (par exemple lu depuis la base)
    public function setMotDePasse(string $motDePasseHache): void {
        $this->motDePasse = $motDePasseHache;
    }

    public function setRole(string $role): void {
        if (!in_array($role, self::ROLES, true)) {
            throw new InvalidArgumentException('Rôle invalide.');
        }

        $this->role = $role;
    }

    // Reçoit un mot de passe EN CLAIR (saisi dans un formulaire) : le vérifie puis le hache
    public function changerMotDePasse(string $motDePasse): void {
        if (mb_strlen($motDePasse) < self::MOT_DE_PASSE_MIN) {
            throw new InvalidArgumentException(Lang::t('inscription.err_mdp'));
        }

        $this->motDePasse = password_hash($motDePasse, PASSWORD_DEFAULT);
    }

    // Compare un mot de passe saisi avec le hash enregistré
    public function verifierMotDePasse(string $motDePasse): bool {
        return password_verify($motDePasse, $this->motDePasse);
    }
}
