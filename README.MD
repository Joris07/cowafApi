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
