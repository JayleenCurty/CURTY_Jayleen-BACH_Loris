# GroupTrip

> Projet libre du cours
> [_Programmation Serveur 2 (ProgServ2)_](https://github.com/heig-vd-progserv-course/heig-vd-progserv2-course)
> – HEIG-VD

🌍 **Site en ligne :
[https://grouptrip.lorisbach.ch](https://grouptrip.lorisbach.ch)**

GroupTrip est une application web qui aide à organiser des voyages en grand
groupe (vacances entre ami·es, EVJF/EVG, voyages d'études, séminaires…) au
départ de l'aéroport de Genève. L'organisateur ou l'organisatrice saisit les
critères du voyage (nombre de personnes, budget, dates, type de destination),
l'application propose des combinaisons vol + hôtel compatibles, puis génère une
checklist des choses à préparer avant le départ.

## Équipe

| Membre            | Rôle principal                                            |
| ----------------- | --------------------------------------------------------- |
| **Loris Bach**    | Développement applicatif (PHP, interface), hébergement    |
| **Jayleen Curty** | Base de données (modélisation, SQL, données), déploiement |

## Sommaire

- [Fonctionnalités](#fonctionnalités)
- [Technologies](#technologies)
- [Structure du projet](#structure-du-projet)
- [Installation en local](#installation-en-local)
- [Configuration de la base de données](#configuration-de-la-base-de-données)
- [Déploiement en production](#déploiement-en-production)
- [Méthode de travail](#méthode-de-travail)
- [Sécurité](#sécurité)
- [Lien maquette](#maquette)
- [Justification IA](#justification-ia)

## Fonctionnalités

- Inscription, connexion et déconnexion (mots de passe hachés, sessions).
- Création, modification et suppression d'un voyage avec ses critères.
- Propositions de combinaisons vol + hôtel au départ de Genève selon le budget,
  les dates et la taille du groupe.
- Choix d'une proposition et récapitulatif du voyage (coût total et par
  personne).
- Checklist avant le départ : ajouter, cocher et supprimer des tâches.
- Tableau de bord « Mes voyages ».
- Espace administrateur pour gérer le catalogue (destinations, vols, hôtels).

Le détail des fonctionnalités principales et optionnelles se trouve dans le
[cahier des charges](docs/CAHIER_DES_CHARGES_V1.md).

## Technologies

- **PHP 8** avec **PDO** (requêtes préparées)
- **MariaDB** / MySQL
- **HTML / CSS**
- **Docker Compose** pour l'environnement de développement (Apache/PHP, MariaDB,
  phpMyAdmin, Mailpit)
- **GitHub Actions** pour le déploiement automatique
- **Infomaniak** pour l'hébergement de production

## Structure du projet

```
grouptrip/
│
├── 📁 .devcontainer/              # Configuration du conteneur de développement
├── 📁 .github/
│   └── 📁 workflows/
│       └── deploy.yml             # Déploiement automatique vers Infomaniak à chaque push sur main
├── 📁 .vscode/                    # Paramètres recommandés pour VS Code
│
├── 📁 config/                     # Configuration de l'application (hors de la racine web)
│   ├── database.example.ini       # Modèle à copier en database.ini (versionné)
│   └── database.ini               # Identifiants réels de connexion (ignoré par Git)
│
├── 📁 docs/                       # Documentation du projet
│   ├── CAHIER_DES_CHARGES_V1.md
│   ├── 📁 maquettes/              # Maquettes et wireframes
│   │   ├── GroupTrip_maquette.pdf
│   │   └── GroupTrip_wireframe_maquette.pdf
│   └── 📁 schema/                 # Modèles de données
│       ├── grouptrip-MCD_GroupTrip.drawio.pdf   # Modèle Conceptuel de Données
│       └── grouptrip-MLD_GroupTrip.drawio.pdf   # Modèle Logique de Données
│
├── 📁 initdb/                     # Scripts SQL de création et de remplissage de la base
│   ├── 01-schema.sql              # Création des tables (MPD)
│   ├── 02-catalogue.sql           # Destinations, vols, hôtels (données fictives)
│   ├── 03-comptes.sql             # Compte administrateur et compte de démo
│   └── 04-taches.sql              # Tâches de la checklist (table taches)
│
├── 📁 public/                     # Racine web : seul dossier accessible depuis le navigateur
│   ├── .user.ini                  # Production : erreurs PHP masquées (display_errors = Off)
│   ├── 📁 css/
│   │   ├── reset.css
│   │   ├── base.css               # Couleurs, header, footer, boutons (toutes les pages)
│   │   ├── accueil.css
│   │   ├── formulaires.css        # Inscription, connexion, créer / modifier / supprimer un voyage
│   │   ├── mes-voyages.css
│   │   ├── propositions.css
│   │   └── recapitulatif.css
│   ├── 📁 images/
│   │   └── 📁 destinations/       # Une photo par destination
│   ├── 📁 admin/                  # (à venir) Pages réservées au rôle administrateur
│   ├── index.php                  # Accueil (public)
│   ├── inscription.php            # Création de compte (public)
│   ├── connexion.php              # Connexion (public)
│   ├── deconnexion.php
│   ├── mes-voyages.php            # Tableau de bord : liste des voyages
│   ├── voyage-creer.php           # Étape 1 : critères du voyage
│   ├── voyage-modifier.php
│   ├── voyage-supprimer.php       # Confirmation + suppression (POST)
│   ├── propositions.php           # Étape 2 : choix du vol et de l'hôtel
│   ├── recapitulatif.php          # Étape 3 : récapitulatif et checklist
│   └── tache.php                  # Ajouter, cocher, supprimer une tâche
│
├── 📁 src/                        # Code back-end, hors de portée du navigateur
│   ├── 📁 utils/
│   │   └── autoloader.php         # Chargement automatique des classes (namespace = dossier)
│   │
│   ├── 📁 classes/
│   │   ├── Database.php           # Connexion PDO à partir de config/database.ini
│   │   ├── Auth.php               # Session : connexion, déconnexion, pages privées
│   │   ├── Lang.php               # Traductions FR / EN (i18n)
│   │   ├── ComptableInterface.php # Contrat compter() : Destinations, Vols, Hotels Managers
│   │   │
│   │   │   # Une entité + un Manager par table du MCD
│   │   ├── 📁 Utilisateurs/       # Table utilisateur
│   │   │   ├── Utilisateur.php                  # Entité : propriétés, getters / setters, validation
│   │   │   └── UtilisateursManager.php          # Requêtes SQL de la table
│   │   ├── 📁 Voyages/            # Table voyage
│   │   │   ├── Voyage.php
│   │   │   ├── VoyagesManager.php
│   │   │   └── VoyagesManagerInterface.php  # Contrat des opérations sur les voyages
│   │   ├── 📁 Taches/             # Table taches
│   │   │   ├── Tache.php
│   │   │   └── TachesManager.php
│   │   ├── 📁 Destinations/       # Table destination
│   │   │   ├── Destination.php
│   │   │   └── DestinationsManager.php
│   │   ├── 📁 Vols/               # Table vol
│   │   │   ├── Vol.php
│   │   │   └── VolsManager.php
│   │   └── 📁 Hotels/             # Table hotel
│   │       ├── Hotel.php
│   │       └── HotelsManager.php
│   │
│   ├── 📁 lang/                   # Textes de l'interface
│   │   ├── fr.php
│   │   └── en.php
│   │
│   └── 📁 partials/               # Fragments HTML réutilisés
│       ├── header.php             # <head>, menu, sélecteur de langue
│       ├── footer.php
│       └── formulaire-voyage.php  # Formulaire commun à voyage-creer et voyage-modifier
│
├── .gitignore                     # Fichiers exclus de Git (dont config/database.ini)
├── compose.yaml                   # Services locaux : Apache/PHP, MariaDB, phpMyAdmin, Mailpit
├── JUSTIFICATION_IA.MD            # Justification de notre utilisation de l'IA
└── README.md                      # Ce fichier
```

La séparation des dossiers suit une règle simple :

- **`public/`** contient uniquement ce que le navigateur peut ouvrir.
- **`src/`** contient la logique de l'application, incluse par les pages de
  `public/`.
- **`config/`** contient la configuration, dont les identifiants de connexion.
- **`initdb/`** contient la base de données sous forme de scripts SQL.

`src/` et `config/` étant placés en dehors de `public/`, ils ne sont jamais
accessibles depuis Internet.

### Organisation des classes

Le code suit le modèle vu en cours (exemple « Tools ») :

- **Une entité par table du MCD** (`Voyage`, `Utilisateur`, `Tache`,
  `Destination`, `Vol`, `Hotel`) : propriétés privées, getters et setters. Les
  setters vérifient les valeurs et lancent une `InvalidArgumentException` si
  elles ne sont pas valides.
- **Un Manager par table** (`VoyagesManager`, ...) : contient toutes les
  requêtes SQL (préparées) de la table et renvoie des objets entités.
- **Deux interfaces** (contrats que les classes doivent respecter) :
  `ComptableInterface` (méthode `compter()`), implémentée par
  `DestinationsManager`, `VolsManager` et `HotelsManager`, et
  `VoyagesManagerInterface`, implémentée par `VoyagesManager`.
- **Un namespace par dossier** (`Voyages\Voyage` →
  `src/classes/Voyages/Voyage.php`), chargé automatiquement par
  `src/utils/autoloader.php`.

## Installation en local

### Prérequis

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- [Visual Studio Code](https://code.visualstudio.com/) avec l'extension
  [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)

### Étapes

1. **Cloner le dépôt**

   ```bash
   git clone https://github.com/JayleenCurty/CURTY_Jayleen-BACH_Loris.git
   ```

2. **Ouvrir le dossier du projet dans VS Code**, puis lancer la commande _« Dev
   Containers: Reopen in Container »_ (`Cmd`+`Shift`+`P` sur macOS,
   `Ctrl`+`Shift`+`P` sur Windows/Linux).

   > Ouvrez bien le dossier du projet lui-même (celui qui contient ce README) et
   > non un dossier parent.

3. **Créer le fichier de configuration** (voir
   [Configuration de la base de données](#configuration-de-la-base-de-données))
   :

   ```bash
   cp config/database.example.ini config/database.ini
   ```

4. **Démarrer les services Docker** :

   ```bash
   docker compose up -d
   ```

   Au premier démarrage, MariaDB exécute automatiquement les scripts du dossier
   `initdb/` dans l'ordre (01 → 04) : les tables sont créées et remplies avec
   les données de test.

5. **Ouvrir l'application** :

| Service                   | Adresse                                                        |
| ------------------------- | -------------------------------------------------------------- |
| Application GroupTrip     | [http://localhost:8080/public/](http://localhost:8080/public/) |
| phpMyAdmin                | [http://localhost:9090](http://localhost:9090)                 |
| Mailpit (e-mails de test) | [http://localhost:8025](http://localhost:8025)                 |

### Réinitialiser la base locale

Les scripts de `initdb/` ne s'exécutent que lorsque la base est vide. Après une
modification de ces scripts, il faut recréer la base :

```bash
docker compose down -v
docker compose up -d
```

> ⚠️ L'option `-v` supprime toutes les données de la base locale.

### Arrêter les services

```bash
docker compose down
```

## Configuration de la base de données

Les informations de connexion à la base de données ne sont pas écrites dans le
code : elles proviennent du fichier de configuration **`config/database.ini`**,
lu par `src/database.php`.

```ini
host = "..."
port = 3306
database = "..."
username = "..."
password = "..."
```

Ce fichier contient des identifiants et n'est donc **jamais versionné** : il est
listé dans `.gitignore`. Seul le modèle `config/database.example.ini` est
présent dans le dépôt.

Chaque environnement possède son propre `database.ini` :

| Environnement           | `host`                      | Identifiants                                     |
| ----------------------- | --------------------------- | ------------------------------------------------ |
| Local (Docker)          | `mariadb`                   | Ceux définis dans `compose.yaml`                 |
| Production (Infomaniak) | `in6xl4.myd.infomaniak.com` | Ceux de la base créée dans le Manager Infomaniak |

Le même code fonctionne ainsi partout : seul le contenu de `database.ini`
change.

## Déploiement en production

L'application est hébergée chez **Infomaniak**, sur l'hébergement web mis à
disposition par la HEIG-VD.

🌍 **URL du site :
[https://grouptrip.lorisbach.ch](https://grouptrip.lorisbach.ch)**

### 1. Dossier racine du site

Dans le Manager Infomaniak, le dossier cible du site `grouptrip.lorisbach.ch`
est défini sur :

```
/sites/grouptrip.lorisbach.ch/public
```

Le serveur n'expose donc que le contenu de `public/`. Les dossiers `config/` et
`src/` sont présents sur le serveur, mais situés au-dessus de la racine web :
ils restent lisibles par PHP, mais inaccessibles depuis le navigateur. Par
exemple, l'adresse `https://grouptrip.lorisbach.ch/config/database.ini` renvoie
une erreur 404.

Le site est servi en **HTTPS** grâce à un certificat Let's Encrypt, avec une
redirection automatique de HTTP vers HTTPS définie dans `public/.htaccess`.

### 2. Envoi des fichiers sur le serveur

Les fichiers sont envoyés automatiquement sur le serveur par **GitHub Actions**,
à chaque push sur la branche `main` (donc à chaque pull request mergée).

Le workflow [`.github/workflows/deploy.yml`](.github/workflows/deploy.yml) :

1. récupère le code du dépôt ;
2. se connecte au serveur FTP d'Infomaniak (`in6xl4.ftp.infomaniak.com`) ;
3. envoie uniquement les fichiers modifiés depuis le dernier déploiement.

**Sécurité du déploiement :**

- Les identifiants FTP sont stockés dans les **secrets du dépôt GitHub**
  (`FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`) et n'apparaissent jamais dans
  le code.
- Le compte FTP utilisé (`in6xl4_deploy`) est un compte dédié, **limité au seul
  dossier du site** et sans accès SSH.

**Fichiers exclus du déploiement**, car ils ne servent qu'au développement :
`.devcontainer/`, `.vscode/`, `.github/`, `docs/`, `initdb/`, `compose.yaml` et
`README.md`.

Le suivi des déploiements se fait dans l'onglet
[Actions](https://github.com/JayleenCurty/CURTY_Jayleen-BACH_Loris/actions) du
dépôt.

> **Règle de l'équipe :** seul du code fonctionnel est mergé sur `main`, puisque
> chaque merge met le site en ligne à jour.

### 3. Création de `config/database.ini` sur le serveur

`database.ini` n'étant pas versionné, il n'est jamais envoyé par GitHub Actions.
Il a été créé **une seule fois, à la main**, directement sur le serveur :

1. Manager Infomaniak → **Hébergement Web** → **FTP / SSH** → **Web FTP**.
2. Ouvrir le dossier `/sites/grouptrip.lorisbach.ch/config/`.
3. Créer le fichier `database.ini` avec les identifiants de la base de
   production :

   ```ini
   host = "in6xl4.myd.infomaniak.com"
   port = 3306
   database = "in6xl4_grouptrip"
   username = "<utilisateur de la base Infomaniak>"
   password = "<mot de passe de la base Infomaniak>"
   ```

Comme il n'existe pas dans le dépôt, ce fichier n'est jamais écrasé ni supprimé
par les déploiements suivants.

### 4. Import des scripts SQL dans phpMyAdmin (Infomaniak)

La base de production `in6xl4_grouptrip` a été créée depuis le Manager
Infomaniak. Les tables et les données y ont ensuite été importées avec le
phpMyAdmin d'Infomaniak :

1. Ouvrir phpMyAdmin depuis le Manager Infomaniak (**Hébergement Web** → **Bases
   de données**).
2. Sélectionner la base **`in6xl4_grouptrip`** dans la colonne de gauche.
3. Ouvrir l'onglet **Importer**.
4. Importer les scripts du dossier `initdb/` **dans l'ordre**, un par un :
   1. `01-schema.sql` – création des tables
   2. `02-catalogue.sql` – destinations, vols et hôtels
   3. `03-comptes.sql` – comptes administrateur et de démonstration
   4. `04-taches.sql` – tâches de la checklist

L'ordre est important : les données dépendent des tables, et les tâches
dépendent des voyages, qui dépendent des comptes.

> Chez un hébergeur, la création d'une base de données n'est pas autorisée en
> SQL. Les scripts ne contiennent donc ni `CREATE DATABASE` ni `USE` : les
> tables sont créées dans la base sélectionnée au moment de l'import.

### Limite connue

Le transfert entre GitHub et Infomaniak se fait en FTP non chiffré, l'outil de
déploiement n'étant pas compatible avec le mode FTPS du serveur. Le risque est
limité (compte dédié restreint au dossier du site, sans SSH, identifiants
stockés dans les secrets GitHub), mais une amélioration possible serait un
déploiement par SFTP/SSH.

## Méthode de travail

Le projet suit le workflow GitHub étudié en cours :

1. une **issue** par tâche ;
2. une **branche** par issue (par exemple `20-config-databaseini`) ;
3. une **pull request** relue par l'autre membre de l'équipe avant d'être mergée
   sur `main` ;
4. un **déploiement automatique** en production à chaque merge sur `main`.

## Sécurité

L'application suit les principes vus en cours (séance « Sécurité et nettoyage
des saisies »).

### Validation des saisies

- **Côté client** : attributs HTML `required`, `type="email"`, `minlength`,
  `min` / `max` sur les formulaires.
- **Côté serveur** : chaque setter des entités vérifie sa valeur et lance une
  `InvalidArgumentException` si elle n'est pas valide (longueur, format d'e-mail
  avec `filter_var`, valeur dans une liste autorisée, nombre dans un intervalle,
  dates). Les pages affichent un message d'erreur sous chaque champ.

### Nettoyage des saisies

- Les saisies sont nettoyées avec `trim()`.
- Tout ce qui est affiché dans une page passe par `htmlspecialchars()`, sauf les
  nombres convertis avec `(int)`.

### Injections SQL

Toutes les requêtes contenant des données sont **préparées** (`prepare` +
`bindValue`) dans les Managers. Les seules requêtes `query()` sont des
`COUNT(*)` sans aucune donnée venant de l'utilisateur.

### Attaques XSS

Les données venant de la base ou de l'utilisateur sont toujours échappées avec
`htmlspecialchars()` avant d'être affichées.

### Tests effectués (saisies de l'exercice 4 du cours)

| Saisie                                                                           | Résultat                                            |
| -------------------------------------------------------------------------------- | --------------------------------------------------- |
| `<script>alert("I <3 JavaScript")</script>` dans le prénom et le nom d'un voyage | Affiché comme du texte, le script ne s'exécute pas  |
| `'); DROP TABLE voyage; --` dans le nom d'un voyage                              | Enregistré comme du texte, les tables sont intactes |
| E-mail `' OR '1'='1` à la connexion                                              | Connexion refusée                                   |
| `type_voyage=pirate` envoyé à la main                                            | Refusé par la validation                            |

### Autres protections

- Mots de passe hachés avec `password_hash()` et vérifiés avec
  `password_verify()`.
- Chaque requête sur un voyage vérifie qu'il appartient à la personne connectée
  (`id_utilisateur` venant de la session, jamais du formulaire).
- La suppression passe par une page de confirmation, puis un formulaire POST
  (jamais par un simple lien).
- Les erreurs PHP ne sont pas affichées en production (`public/.user.ini` :
  `display_errors = Off`), pour ne pas révéler de chemins ni de noms de tables.
  Vérifié avec une page de test sur Infomaniak.
- Les identifiants de la base sont dans `config/database.ini`, hors de `public/`
  et ignoré par Git.

## Lien maquette Figma

- 🎨
  [Maquette Figma](https://www.figma.com/design/l5XSbpdj9I4OESRRqoHF9t/GroupTrip?node-id=0-1&t=2pAHEcaONCwkAPRT-1)

## Justification IA

Concernant l'utilisation de l'IA dans notre projet tout est détaillé dans le
fichier "JUSTIFICATION_IA.MD" sur notre git.
