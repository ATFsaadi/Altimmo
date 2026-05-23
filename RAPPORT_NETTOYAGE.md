# Rapport de nettoyage

## Problemes constates dans l'ancien projet

- `includes/config.php` manquait, ce qui cassait la connexion a la base.
- `database.sql` etait mentionne dans le README mais absent de l'archive.
- Les pages PHP etaient toutes a la racine, avec HTML, SQL et logique metier melanges.
- Plusieurs requetes utilisaient des noms de tables ou colonnes incoherents : `userss`, `messages`, `id_agent`, `id_biens`, `mot_de_passe`.
- Certaines requetes etaient vulnerables aux injections SQL, notamment les recherches et le profil.
- `includes/header.php` fermait deja `body` et `html`, ce qui cassait la structure HTML des pages.
- Des fichiers racine etaient des doublons ou de simples redirections inachevees : `ajout_bien.php`, `gestion_biens.php`, `pages.php`.
- Des images avaient des noms tres longs et difficiles a maintenir.

## Changements effectues

- Creation d'une architecture MVC simple : `controleur`, `modele`, `vue`, `config`, `assets`.
- Mise en place d'un point d'entree unique : `index.php`.
- Separation claire des vues client et admin.
- Centralisation de la connexion PDO dans `config/database.php`.
- Creation de modeles dedies : utilisateurs, biens, categories, messages.
- Nettoyage des routes avec `index.php?page=...`.
- Remplacement des requetes directes des vues par des appels aux modeles.
- Ajout d'un fichier `database.sql` complet.
- Renommage et rangement des assets utiles dans `assets/img`.
- Suppression des anciennes pages plates dans la version MVC finale.

## A verifier de votre cote

- Le nom de base dans `config/config.php` : par defaut `altimmo`.
- Le mot de passe MySQL : par defaut vide, comme souvent avec Wamp.
- Importer `database.sql` avant d'ouvrir le site.
- Si vous voulez reprendre une ancienne base, alignez les colonnes sur le schema fourni.

