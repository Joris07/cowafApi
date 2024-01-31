## Co'waf

Application de mise en relation de propriétaires et associations d'animaux pour du covoiturage.

Un projet PWA développé avec Vue.js, Symfony API, Docker et MySQL.

# Clonez le projet depuis GitHub :

   ```bash
   git clone https://github.com/Joris07/cowafApi.git
   ```

## Travailler en local

## Prérequis

- PHP
- Composer
- Symfony CLI
- MySQL (ou tout autre SGBD compatible)

1. Lancer le projet avec docker :
   ```bash
   cd cowafApi

   composer install
   ```

2. Base de données 
    - Il faut modifier le .env pour lier avec la bdd
    - Migration
    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    ```

3. Lancement du serveur
    ```bash
    symfony server:start
    ```
    - http://localhost:8000

## Travailler avec Docker

## Prérequis

- Docker
- Docker Compose

## Installation

1. Lancer le projet avec docker :

   ```bash
   cd cowafApi/docker

   docker-compose up --build
   ```

2. Accès à l'Application :
    - L'application Vue.js est accessible à l'URL : http://localhost:40110
    - L'API Symfony est accessible à l'URL : http://localhost:40111
    - PhpMyAdmin est accessible à l'URL : http://localhost:40113

3. Identifiants de Connexion :

    1. PhpMyAdmin :
        - URL : http://localhost:40113
        - Server : mysql-db
        - Utilisateur : cowafUser
        - Mot de Passe : w8yh9QLt5ibbcTN6xBXaj1