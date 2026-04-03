StageLab - Plateforme de Gestion de Stages
StageLab est une application web de gestion de stages développée en PHP 8 suivant une architecture MVC personnalisée. Elle permet la mise en relation entre étudiants, entreprises et pilotes de promotion.

📋 Prérequis
PHP : version 8.0 ou supérieure.

PHP MyAdmin

Composer : pour la gestion des dépendances.

Serveur Web : Apache (avec mod_rewrite activé)

🛠️ Installation
1. Clonage du projet
Bash
git clone https://github.com/MishaD06/Projet-dev-web-Web4All
cd stagelab
2. Gestion des dépendances
Installez les bibliothèques nécessaires (Twig, PHPUnit, etc.) via Composer :

Bash
composer install
3. Configuration de la base de données
Ouvrez phpMyAdmin et créez une nouvelle base de données nommée stagelab.

Cliquez sur l'onglet Importer.

Sélectionnez et exécutez le fichier database/schema.sql pour créer la structure.

Répétez l'opération avec le fichier database/seed.sql pour charger les données de test (comptes, offres, entreprises).

4. Configuration de l'environnement
Créez un fichier .env à la racine du projet et configurez vos accès :

Ini, TOML
DB_HOST=localhost
DB_NAME=stagelab
DB_USER=root
DB_PASS=votre_mot_de_passe
5. Serveur Web
Configurez votre serveur pour que le Document Root pointe vers le dossier /public.

Apache : Le fichier .htaccess gère déjà la réécriture d'URL vers index.php.

Nginx : Assurez-vous de rediriger toutes les requêtes vers public/index.php.

🏗️ Architecture
Le projet suit une structure MVC stricte pour assurer la maintenabilité :

/app/Core : Moteur du projet (Routeur, Authentification, Base de données).

/app/Controllers : Logique de traitement des requêtes.

/app/Models : Interaction avec la base de données via PDO.

/app/Views : Templates Twig pour le rendu HTML.

/public : Seul dossier accessible publiquement (Assets CSS/JS, Index).

🔒 Sécurité Implémentée
Ce projet intègre plusieurs couches de protection :

Injections SQL : Requêtes préparées systématiques via PDO.

Failles XSS : Auto-escaping natif du moteur Twig.

Failles CSRF : Protection par jetons sur tous les formulaires POST (Suppression, Mise à jour).

Path Traversal : Sécurisation des téléchargements via basename() sur les fichiers systèmes.

Uploads RCE : Validation du type MIME réel et renommage aléatoire des fichiers uploadés.

🧪 Tests Unitaires
Le projet utilise PHPUnit pour valider la logique métier.
Pour exécuter les tests :

Bash
php vendor/bin/phpunit tests/OfferControllerTest.php

🔑 Identifiants par défaut (Seed)

user : admin@viacesi.fr   mdp : Web4All?
