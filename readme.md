# Survivors Arena — Arène de combat en PHP orienté objet

**Démo en ligne :** https://fiol.projetsmmichamps.fr/POO2/

Petit projet PHP/MySQL orienté objet autour d’un système de personnages et de combats (univers inspiré de *The Last of Us*).

## Fonctionnalités

- Affichage des personnages sous forme de cartes (stats, rareté, faction, image, description).
- Sélection d’un attaquant et d’une cible depuis l’interface `index.php`.
- Combat en tour par tour avec narration :
	- dégâts avec variance,
	- esquive,
	- coup critique,
	- attaque puissante liée à l’énergie,
	- contre-attaque automatique si la cible survit.
- Gestion des KO + bouton de réanimation globale des personnages KO.
- Mini back-office `admin.php` pour CRUD basique (ajout, modification, suppression).

## Stack technique

- PHP 8+
- MySQL / MariaDB
- PDO (requêtes préparées)
- HTML/CSS (pas de framework)

## Structure du dossier

- `index.php` : page principale (combat + affichage des cartes)
- `admin.php` : interface d’administration CRUD
- `personnage.php` : classe principale `Personnage` (stats, logique d’attaque, hydratation)
- `personnageManager.php` : accès base de données (getAll, getById, add, update, delete)
- `guerrier.php` : spécialisation avec bonus d’attaque
- `guerisseur.php` : spécialisation avec régénération renforcée
- `config.example.php` : modèle de configuration de connexion PDO (singleton), à copier en `config.php`
- `perso_db.sql` : structure + données de départ
- `assets/` : styles et images
- `combat.php`, `attaque.php`, `deck.php`, `statut.php` : bases de classes pour extensions futures

## Installation locale (WAMP)

### 1) Cloner / copier le projet

```bash
git clone https://github.com/allaxan/survivors-arena.git
```

Placer le dossier dans le répertoire web de WAMP (ex. `c:/wamp64/www/survivors-arena`).

### 2) Importer la base

Importer `perso_db.sql` via phpMyAdmin.

### 3) Configurer la connexion

Copier `config.example.php` en `config.php`, puis renseigner les identifiants de ta base MySQL (`dbname`, `username`, `password`).

> `config.php` est ignoré par Git pour ne pas publier les identifiants.

### 4) Lancer le projet

Dans le navigateur :

- `http://localhost/survivors-arena/index.php`
- `http://localhost/survivors-arena/admin.php`

## Modèle de données (table `personnages`)

Champs principaux :

- `id`
- `nom`
- `type`
- `atk`
- `defense`
- `pv`
- `energie`
- `rarete`
- `faction`
- `description`
- `image`

## Règles de combat (résumé)

- Un personnage KO (`pv <= 0`) ne peut plus attaquer.
- Dégâts basés sur `atk`, réduits en partie par la `defense` adverse.
- Variance aléatoire de dégâts (±10%).
- Chance d’esquive calculée depuis la défense.
- Chance de critique augmentée par l’énergie.
- Si l’énergie est suffisante, attaque puissante consommant de l’énergie.

## Limites actuelles / pistes d’amélioration éventuelles 

- `admin.php` gère surtout `nom`, `atk`, `pv` (pas tous les attributs avancés).
- Pas d’authentification sur l’admin.
- Pas de tests automatisés.
- Les classes `Combat`, `Attaque`, `Deck`, `Statut` sont encore des bases à enrichir.
