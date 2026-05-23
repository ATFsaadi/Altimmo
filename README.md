# Altimmo - Plateforme Immobilière

Altimmo est une application web de gestion immobilière permettant aux agents et aux clients de consulter des biens immobiliers, d'échanger des messages et de gérer les annonces.

## Fonctionnalités

- **Consultation des biens immobiliers** : Recherche et visualisation des propriétés disponibles
- **Espace agent** : Gestion des biens immobiliers (ajout, modification, suppression)
- **Messagerie intégrée** : Communication entre clients et agents concernant les biens
- **Gestion des utilisateurs** : Différents niveaux d'accès (utilisateur, agent, administrateur)

## Technologies utilisées

- PHP
- MySQL
- HTML/CSS
- Bootstrap
- JavaScript

## Installation

1. Clonez ce dépôt sur votre serveur web :
```
git clone https://github.com/votre-username/altimmo.git
```

2. Importez la base de données en utilisant le fichier `database.sql`

3. Configurez votre connexion à la base de données dans les fichiers appropriés

4. Accédez à l'application via votre navigateur web

## Structure du projet

- `index.php` : Page d'accueil
- `bien.php` : Détail d'un bien immobilier
- `login.php` : Connexion utilisateur
- `messages.php` : Système de messagerie
- `gestion_biens.php` : Interface de gestion des biens pour les agents
- `includes/` : Fichiers inclus (header, footer, etc.)
- `img/` : Images du site et des biens immobiliers

## Niveaux d'accès

- **Niveau 1** : Utilisateur standard (consultation et messages)
- **Niveau 2** : Agent immobilier (gestion des biens et réponse aux messages)
- **Niveau 3** : Administrateur (toutes les fonctionnalités)
# agence-immi
# agence-immi
