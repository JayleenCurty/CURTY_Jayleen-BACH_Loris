# Explication utilisation IA

Chaque fin de semaine nous utilisons le prompt suivant afin d'être le plus
transparent possible concernant notre utilisation de l'IA. Le voici : "j'aimerai
que tu me résumes tout ce que je t'ai demandé cette semaine concernant le projet
GroupTrip, pour que je puisse mettre dans mon fichier justification_ia.md l'aide
que tu m'as apporté afin d'être le plus transparent possible."

## IA utilisée pour création de maquettes - SEMAINE 3

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

## IA utilisée pour la configuration et le déploiement - SEMAINE 5

Nous avons utilisé Claude (Anthropic) comme assistant pour la configuration de
la connexion à la base de données et la mise en ligne de l'application. Ces
sujets n'avaient pas encore été vus en détail et nous ont posé plusieurs
blocages. L'IA nous a servi à **comprendre** les notions et à **diagnostiquer**
les erreurs. Nous avons réalisé nous-mêmes toutes les manipulations (code, Git,
Manager Infomaniak, GitHub) et vérifié chaque résultat.

### Fichier de configuration de la base de données

- **Besoin** : respecter le critère « les informations de connexion à la base de
  données proviennent d'un fichier de configuration », sans exposer de mot de
  passe sur GitHub.
- **Aide reçue** :
  - explication du principe `config/database.ini` (identifiants réels, non
    versionné) et `config/database.example.ini` (modèle versionné) ;
  - code de `src/database.php`, qui lit le fichier avec `parse_ini_file()` et
    crée la connexion PDO ;
  - ajout de `config/database.ini` dans le `.gitignore`.
- **Ce que nous avons compris** :
  - phpMyAdmin n'est qu'un outil pour consulter la base : le site PHP doit aussi
    s'y connecter, avec ses propres identifiants ;
  - `compose.yaml` sert à Docker pour **créer** la base locale, et
    `database.ini` sert à PHP pour **s'y connecter** ;
  - chaque environnement (local, production) a son propre `database.ini`.

### Résolution d'erreurs

- **Connexion qui tournait dans le vide** (`Connection timed out`) en local :
  notre `database.ini` local pointait vers la base Infomaniak, inaccessible
  depuis l'extérieur. Nous l'avons corrigé avec l'hôte `mariadb` et les
  identifiants de `compose.yaml`.
- **`database.ini` envoyé par erreur sur GitHub** : le fichier avait été commité
  avant que la règle du `.gitignore` ne s'applique. L'IA nous a expliqué la
  commande `git rm --cached` pour que Git arrête de le suivre. Le fichier a
  ensuite disparu en local après un `git pull`, et nous l'avons recréé à partir
  du modèle.

### Mise en ligne sur Infomaniak

- **Hébergement** : l'IA nous a expliqué la différence entre un nom de domaine
  et un hébergement, et nous a aidés à comprendre que notre site était hébergé
  sur le sous-domaine `grouptrip.lorisbach.ch`.
- **Dossier racine** : explication du principe de racine web. Le site pointe
  désormais sur `/sites/grouptrip.lorisbach.ch/public`, pour que `config/` et
  `src/` ne soient pas accessibles depuis le navigateur. Nous l'avons vérifié en
  testant l'accès direct à `config/database.ini`.
- **HTTPS** : le certificat Let's Encrypt était déjà présent. L'IA nous a
  expliqué pourquoi le site restait accessible en HTTP et comment forcer la
  redirection vers HTTPS (`.htaccess`).

### Déploiement automatique avec GitHub Actions

- **Besoin** : éviter de renvoyer les fichiers à la main sur le serveur après
  chaque modification.
- **Aide reçue** :
  - proposition d'un workflow GitHub Actions (`.github/workflows/deploy.yml`)
    utilisant l'action `SamKirkland/FTP-Deploy-Action` ;
  - explication des secrets GitHub (`FTP_SERVER`, `FTP_USERNAME`,
    `FTP_PASSWORD`) pour ne jamais écrire les identifiants dans le code ;
  - conseil de créer un compte FTP dédié, limité au dossier du site et sans SSH
    ;
  - liste des fichiers à exclure du déploiement (`docs/`, `initdb/`,
    `.devcontainer/`…).
- **Erreurs résolues** :
  - `425 Unable to build data connection` : incompatibilité entre l'action et le
    mode FTPS d'Infomaniak. Nous sommes passés en FTP simple, en documentant
    cette limite dans le README ;
  - workflow resté en file d'attente (_Queued_) : simple attente d'une machine
    GitHub.

### Ce que nous retenons

L'IA nous a fait gagner beaucoup de temps sur des problèmes de configuration que
nous n'aurions pas su diagnostiquer seuls, notamment en lisant les messages
d'erreur avec nous. Nous avons veillé à comprendre chaque étape avant de
l'appliquer, et nous sommes capables d'expliquer le fonctionnement de la
configuration et du déploiement mis en place. Le code de l'application (pages,
requêtes, logique métier) reste écrit par nous.
