-- =====================================================================
-- GroupTrip - Modèle Physique de Données (MPD)
-- SGBD : MariaDB
-- À importer dans la base créée par le template (via phpMyAdmin)
-- =====================================================================
-- CREATE DATABASE IF NOT EXISTS grouptrip
-- CHARACTER SET utf8mb4
-- COLLATE utf8mb4_unicode_ci;
-- USE grouptrip;
-- Permet de relancer le script pendant le développement
DROP TABLE IF EXISTS taches;

DROP TABLE IF EXISTS voyage;

DROP TABLE IF EXISTS hotel;

DROP TABLE IF EXISTS vol;

DROP TABLE IF EXISTS destination;

DROP TABLE IF EXISTS utilisateur;

-- ---------------------------------------------------------------------
-- Table utilisateur
-- ---------------------------------------------------------------------
CREATE TABLE
    utilisateur (
        id INT NOT NULL AUTO_INCREMENT,
        nom VARCHAR(50) NOT NULL,
        prenom VARCHAR(50) NOT NULL,
        email VARCHAR(255) NOT NULL,
        mot_de_passe VARCHAR(255) NOT NULL,
        role ENUM ('user', 'admin') NOT NULL DEFAULT 'user',
        PRIMARY KEY (id),
        UNIQUE KEY uk_utilisateur_email (email)
    );

-- ---------------------------------------------------------------------
-- Table destination
-- ---------------------------------------------------------------------
CREATE TABLE
    destination (
        id INT NOT NULL AUTO_INCREMENT,
        pays VARCHAR(100) NOT NULL,
        ville VARCHAR(100) NOT NULL,
        code_aeroport CHAR(3) NOT NULL,
        description TEXT NULL,
        image VARCHAR(255) NULL, -- nom du fichier dans public/images/destinations/
        type_destination ENUM ('plage', 'ville', 'montagne', 'nature') NOT NULL,
        ville_en VARCHAR(100) NOT NULL,
        pays_en VARCHAR(100) NOT NULL,
        description_en TEXT NULL,
        PRIMARY KEY (id)
    );

-- ---------------------------------------------------------------------
-- Table vol (un vol = un aller-retour depuis Genève)
-- ---------------------------------------------------------------------
CREATE TABLE
    vol (
        id INT NOT NULL AUTO_INCREMENT,
        compagnie VARCHAR(100) NOT NULL,
        date_aller DATETIME NOT NULL, -- départ de l'aller
        date_retour DATETIME NOT NULL, -- départ du retour
        duree_vol SMALLINT NOT NULL, -- en minutes
        prix_personne DECIMAL(8, 2) NOT NULL, -- prix de l'aller-retour
        places_disponibles SMALLINT NOT NULL,
        id_destination INT NOT NULL,
        PRIMARY KEY (id),
        CONSTRAINT fk_vol_destination FOREIGN KEY (id_destination) REFERENCES destination (id),
        CONSTRAINT chk_vol_dates CHECK (date_retour > date_aller),
        CONSTRAINT chk_vol_prix CHECK (prix_personne >= 0)
    );

-- ---------------------------------------------------------------------
-- Table hotel
-- ---------------------------------------------------------------------
CREATE TABLE
    hotel (
        id INT NOT NULL AUTO_INCREMENT,
        nom VARCHAR(150) NOT NULL,
        etoiles TINYINT NULL,
        prix_nuit DECIMAL(8, 2) NOT NULL, -- par personne et par nuit
        capacite SMALLINT NOT NULL,
        id_destination INT NOT NULL,
        PRIMARY KEY (id),
        CONSTRAINT fk_hotel_destination FOREIGN KEY (id_destination) REFERENCES destination (id),
        CONSTRAINT chk_hotel_etoiles CHECK (etoiles BETWEEN 1 AND 5),
        CONSTRAINT chk_hotel_prix CHECK (prix_nuit >= 0)
    );

-- ---------------------------------------------------------------------
-- Table voyage
-- ---------------------------------------------------------------------
CREATE TABLE
    voyage (
        id INT NOT NULL AUTO_INCREMENT,
        nom VARCHAR(100) NULL,
        type_voyage ENUM (
            'amis',
            'evjf_evg',
            'etudes',
            'seminaire',
            'autre'
        ) NULL,
        nbr_personne SMALLINT NOT NULL,
        budget_personne DECIMAL(8, 2) NOT NULL,
        date_aller DATE NOT NULL,
        date_retour DATE NOT NULL,
        statut ENUM (
            'en_preparation',
            'vol_choisi',
            'hotel_choisi',
            'pret'
        ) NULL DEFAULT 'en_preparation',
        type_destination ENUM ('plage', 'ville', 'montagne', 'nature') NOT NULL,
        id_utilisateur INT NOT NULL,
        id_vol INT NULL, -- NULL tant que le vol n'est pas choisi
        id_hotel INT NULL, -- NULL tant que l'hôtel n'est pas choisi
        PRIMARY KEY (id),
        CONSTRAINT fk_voyage_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id),
        CONSTRAINT fk_voyage_vol FOREIGN KEY (id_vol) REFERENCES vol (id),
        CONSTRAINT fk_voyage_hotel FOREIGN KEY (id_hotel) REFERENCES hotel (id),
        CONSTRAINT chk_voyage_dates CHECK (date_retour > date_aller),
        CONSTRAINT chk_voyage_personnes CHECK (nbr_personne >= 2),
        CONSTRAINT chk_voyage_budget CHECK (budget_personne > 0)
    );

-- ---------------------------------------------------------------------
-- Table taches (checklist avant le départ)
-- ---------------------------------------------------------------------
CREATE TABLE
    taches (
        id INT NOT NULL AUTO_INCREMENT,
        libelle VARCHAR(255) NOT NULL,
        faite BOOLEAN NOT NULL DEFAULT FALSE,
        id_voyage INT NOT NULL,
        PRIMARY KEY (id),
        CONSTRAINT fk_taches_voyage FOREIGN KEY (id_voyage) REFERENCES voyage (id)
    );
