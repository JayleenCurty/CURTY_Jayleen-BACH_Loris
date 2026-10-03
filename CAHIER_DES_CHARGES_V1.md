# Cahier des charges V1 – GroupTrip

> Projet libre – Cours ProgServ2, HEIG-VD Nom de travail : **GroupTrip** (sera
> modifié si on trouve mieux) Version : 1.0 – cahier des charges initial

## 1. Membre de l'équipe

| Membre      | Rôle principal                          | Responsabilités principales                                                                                                 |
| ----------- | --------------------------------------- | --------------------------------------------------------------------------------------------------------------------------- |
| **Loris**   | Développement applicatif (PHP / front)  | Architecture PHP, authentification, formulaires, logique métier, interface utilisateur, déploiement                         |
| **Jayleen** | Base de données (SQL) et données métier | Modélisation (MCD/MLD), scripts de création, jeu de données (destinations, vols, hôtels, checklists), requêtes de recherche |

Les deux membres participent aux revues de code (pull requests), aux tests et à
la documentation.

## 2. Contexte et objectif

Organiser un voyage à plusieurs (10, 15, 30 personnes) est compliqué : il faut
trouver une destination qui convient au budget de chacun, des vols avec assez de
places, un hébergement adapté à la taille du groupe, et ne rien oublier avant le
départ.

**GroupTrip** est une application web qui guide l'organisateur·rice d'un voyage
de groupe, étape par étape :

1. il ou elle saisit les critères du voyage (nombre de personnes, budget, dates,
   type de voyage…) ;
2. l'application propose des combinaisons **destination + vol + hôtel**
   compatibles, au départ de l'aéroport de Genève ;
3. une fois une proposition choisie, l'application génère une **liste de tâches
   (to-do)** des choses à préparer avant le départ.

L'application est un outil d'aide à l'organisation : elle **ne réserve pas** et
**n'encaisse aucun paiement**. Les données (destinations, vols, hôtels) sont
stockées dans notre propre base de données.

### 2.1 Public cible

- Habitant·es de Suisse romande ; tous les départs se font depuis **Genève
  Aéroport (GVA)**.
- Types de voyages : vacances entre ami·es, EVJF/EVG, voyages d'études,
  séminaires d'entreprise, sorties de sociétés ou d'associations.

## 3. Structure du projet

```text
grouptrip/
│
├── 📁 .devcontainer/              # Configuration du conteneur de développement
├── 📁 .vscode/                    # Paramètres recommandés pour VS Code
│
├── 📁 docs/                       # Documentation du projet
│   ├── cahier-des-charges.md
│   ├── 📁 maquettes/              # Wireframes (PDF)
│   └── 📁 schema/                 # Modèles de données
│       ├── MCD.png                # Modèle Conceptuel de Données
│       └── MLD.png                # Modèle Logique de Données
│
├── 📁 initdb/                     # Scripts exécutés à la création de la base MariaDB
│   ├── 01-schema.sql              # Création des tables
│   ├── 02-catalogue.sql           # Destinations, vols, hôtels (données fictives)
│   ├── 03-checklists.sql          # Modèles de checklist
│   └── 04-comptes.sql             # Compte administrateur et compte de démo
│
├── 📁 public/                     # Racine web, seul dossier accessible par le navigateur
│   ├── 📁 css/
│   │   ├── reset.css
│   │   ├── base.css               # Typographie, couleurs, navigation, boutons
│   │   ├── accueil.css
│   │   ├── formulaires.css
│   │   ├── propositions.css
│   │   └── recapitulatif.css
│   ├── 📁 images/
│   │   ├── 📁 destinations/       # Visuels des villes
│   │   └── 📁 logo/
│   ├── 📁 admin/                  # Pages réservées au rôle administrateur
│   │   ├── index.php
│   │   ├── destinations.php
│   │   ├── vols.php
│   │   ├── hotels.php
│   │   └── checklists.php
│   ├── index.php                  # Accueil
│   ├── inscription.php
│   ├── connexion.php
│   ├── deconnexion.php
│   ├── mes-voyages.php            # Tableau de bord
│   ├── voyage-creer.php           # Étape 1 : critères
│   ├── voyage-modifier.php
│   ├── voyage-supprimer.php
│   ├── propositions.php           # Étape 2 : destination + vol + hôtel
│   ├── recapitulatif.php          # Étape 3 : récapitulatif et checklist
│   └── tache.php                  # Ajouter, cocher, supprimer une tâche
│
├── 📁 src/                        # Code back-end, hors de portée du navigateur
│   ├── database.php               # Connexion PDO
│   ├── auth.php                   # Session, exiger_connexion(), exiger_admin()
│   ├── validation.php             # Validation des formulaires côté serveur
│   ├── functions.php              # Utilitaires (échappement HTML, formatage CHF)
│   ├── 📁 requetes/               # Requêtes préparées, un fichier par domaine
│   │   ├── utilisateurs.php
│   │   ├── voyages.php
│   │   ├── propositions.php       # Recherche des combinaisons compatibles
│   │   ├── taches.php
│   │   └── catalogue.php
│   └── 📁 partials/               # Fragments HTML réutilisés
│       ├── header.php
│       ├── nav.php
│       └── footer.php
│
├── .env.example                   # Variables d'environnement à copier en .env
├── .gitignore
├── compose.yaml                   # Services Apache/PHP, MariaDB, Mailpit
└── README.md                      # Documentation du projet (ce fichier)
```

## 4. Fonctionnalités principales

### 4.1 Gestion des comptes utilisateur

- Inscription (nom, e-mail, mot de passe).
- Connexion / déconnexion avec gestion de session.
- Mots de passe hachés (`password_hash` / `password_verify`).
- Seul un utilisateur connecté peut créer et consulter ses voyages.

### 4.2 Création d'un projet de voyage

L'organisateur·rice crée un voyage en saisissant ses critères via un formulaire
:

- nom du voyage ;
- type de voyage (ami·es, EVJF/EVG, voyage d'études, séminaire, autre) ;
- nombre de participant·es ;
- budget maximum **par personne** (en CHF) ;
- dates de départ et de retour (ou durée en nuits) ;
- type de destination souhaité (plage, ville, montagne, nature…) ;
- durée de vol maximale acceptée (optionnel).

Toutes les saisies sont validées côté serveur (champs obligatoires, valeurs
numériques, cohérence des dates).

### 4.3 Proposition de destinations

À partir des critères, l'application interroge la base de données et affiche les
combinaisons **destination + vol + hôtel** qui respectent :

- un vol depuis GVA vers la destination aux dates voulues ;
- un nombre de places disponibles suffisant pour le groupe ;
- un hôtel avec une capacité suffisante pour le groupe ;
- un coût total estimé (vol + hébergement) inférieur ou égal au budget.

Pour chaque proposition, on affiche : la destination, le vol (compagnie,
horaires, durée), l'hôtel, le **coût total** et le **coût par personne**. Les
résultats peuvent être triés par prix.

### 4.4 Choix et récapitulatif

- L'organisateur·rice sélectionne une proposition et l'associe à son voyage.
- Une page récapitulative présente le voyage choisi : dates, vol, hôtel, budget
  prévu vs coût estimé.

### 4.5 Liste de tâches avant le départ

- Une checklist est générée automatiquement selon la destination et le type de
  voyage (ex. : vérifier la validité des pièces d'identité, assurance voyage,
  adaptateur électrique, formalités d'entrée, collecte de l'argent auprès des
  participant·es…).
- L'utilisateur peut cocher / décocher les tâches, en ajouter de nouvelles et en
  supprimer.

### 4.6 Tableau de bord « Mes voyages »

- Liste des voyages de l'utilisateur avec leur statut (en préparation /
  destination choisie / prêt).
- Modification et suppression d'un voyage (CRUD complet).

### 4.7 Administration du catalogue

- Un compte administrateur peut ajouter, modifier et supprimer des destinations,
  vols, hôtels et modèles de checklist.
- Les pages d'administration ne sont accessibles qu'au rôle administrateur.

## 5. Fonctionnalités optionnelles (si le temps le permet)

- **Invitation des participant·es** par e-mail (testable via Mailpit) avec accès
  en lecture au voyage.
- **Vote** des participant·es entre plusieurs propositions de destination.
- **Répartition des tâches** de la checklist entre participant·es.
- **Suivi des paiements** : qui a déjà versé sa part à l'organisateur·rice.
- **Activités** suggérées par destination (visites, soirées, activités de
  groupe).
- **Export PDF ou version imprimable du récapitulatif.**
- **Filtres avancés** : climat, météo moyenne selon le mois, vols directs
  uniquement.
- **Réinitialisation du mot de passe par e-mail.\***

## 6. Hors périmètre

- Réservation réelle de vols ou d'hôtels et paiement en ligne.
- Connexion à des API externes de prix en temps réel (les données sont fictives
  mais réalistes et stockées en base).
- Départs depuis un autre aéroport que Genève.

## 7. Contraintes techniques

- Langage serveur : **PHP 8** (template du cours : Apache + PHP, Docker
  Compose).
- Base de données : **MariaDB**, accès via **PDO** avec requêtes préparées.
- Interface : HTML / CSS (JavaScript minimal si nécessaire).
- Sécurité : protection contre les injections SQL (requêtes préparées) et le XSS
  (échappement des sorties), hachage des mots de passe, contrôle d'accès par
  session et par rôle.
- Gestion de version : dépôt GitHub, branches par fonctionnalité et pull
  requests relues par l'autre membre.
- Déploiement : application accessible sur Internet en fin de projet.

## 8. Modèle de données (première ébauche)

Entités prévues (à affiner lors de la modélisation) :

- `user` (id, nom, email, mot_de_passe, role)
- `trip` (id, user_id, nom, type, nb_personnes, budget_par_personne,
  date_depart, date_retour, type_destination, statut)
- `destination` (id, ville, pays, type_destination, description)
- `flight` (id, destination_id, compagnie, date_depart, date_retour, duree,
  prix_par_personne, places_disponibles)
- `hotel` (id, destination_id, nom, prix_par_nuit_par_personne, capacite)
- `trip_selection` (trip_id, flight_id, hotel_id)
- `checklist_template` (id, type_voyage, type_destination, libelle)
- `task` (id, trip_id, libelle, faite)

## 9. Justification IA

### 9.1 IA utilisée pour création de maquette

Nous avons utilisé l'IA pour la création des maquettes afin de pouvoir avancer
plus vite sur les autres aspects du projets. En effet, nous avons jugé plus
judicieux de passer moins de temps sur cette étape là afin de se laisser de plus
grandes marges pour les autres étapes de notre ambitieux projet.

L'IA n'a pas décidé du contenu à notre place. Nous lui avons fourni notre cahier
des charges, rédigé par nos soins, et elle en a tiré une proposition visuelle :
accueil, création d'un voyage, propositions, récapitulatif avec checklist et
tableau de bord « Mes voyages ». Nous avons ensuite relu chaque écran pour
vérifier qu'il correspondait bien aux fonctionnalités prévues, et corrigé ou
écarté ce qui n'était pas cohérent avec notre périmètre.

Les maquettes restent un support de réflexion. Elles ont été générées avec Figma
AI et Lovable sous forme de prototype côté navigateur, avec des données
fictives, et aucun code issu de cet outil ne sera repris dans l'application
finale. L'ensemble du code PHP, des requêtes SQL et de la structure de la base
sera écrit par nous.

Nous présentons deux maquettes, car nous n'avons pas encore tranché entre les
deux directions et que chacune nous plaît pour des raisons différentes. La
première organise l'application en onglets (Parcours, Mes voyages, Propositions,
Préparatifs, Connexion / compte) et laisse l'utilisateur passer librement d'une
vue à l'autre. La seconde suit un parcours guidé en trois étapes (création,
résultats, récapitulatif), avec un tableau de bord plus détaillé pour retrouver
ses voyages.

Les deux couvrent les mêmes fonctionnalités du cahier des charges. Le choix ne
touche donc que la présentation : la base de données, les requêtes et la logique
PHP restent identiques dans les deux cas. C'est pourquoi nous préférons ne pas
nous restreindre à ce stade. Nous déciderons une fois les fonctionnalités
principales en place, en fonction du temps qu'il nous restera pour l'interface.

## 10. Conclusion

_Cette section sera complétée en fin de projet : fonctionnalités réellement
implémentées, difficultés rencontrées, écarts par rapport au cahier des charges
initial et pistes d'amélioration._

```

```
