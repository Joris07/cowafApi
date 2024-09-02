
# API Cowaf - Documentation

Bienvenue dans la documentation de l'API de l'application de mise en relation de propriétaires d'animaux et d'associations pour l'organisation de covoiturages. Cette API a été développée avec le framework Symfony.

## Table des matières

- [Pré-requis](#pré-requis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [Tests](#tests)
- [Déploiement](#déploiement)
- [Contribuer](#contribuer)
- [Support](#support)
- [Licence](#licence)

## Pré-requis

Avant d'utiliser cette API, assurez-vous d'avoir installé les éléments suivants :

- PHP >= 8.1
- Composer
- Symfony CLI
- MySQL
- Serveur web (Apache, Nginx, etc.)

## Installation

1. Clonez le dépôt :

   ```bash
   git clone https://github.com/Joris07/cowafApi.git
   ```

2. Accédez au répertoire du projet :

   ```bash
   cd votre-repo
   ```

3. Installez les dépendances avec Composer :

   ```bash
   composer install
   ```

4. Créez le fichier `.env.local` pour configurer vos variables d'environnement, en vous basant sur le fichier `.env`.

5. Générez la clé de sécurité JWT (si nécessaire pour l'authentification) :

   ```bash
   php bin/console lexik:jwt:generate-keypair
   ```

6. Mettez en place la base de données :

   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:schema:update --force
   ```

7. (Optionnel) Chargez les données de test :

   ```bash
   php bin/console doctrine:fixtures:load
   ```

## Configuration

Les principales configurations de l'API sont situées dans le fichier `.env` et `config/packages/*.yaml`.

### Configuration JWT

L'authentification via JWT nécessite une clé privée et publique, générée à l'étape d'installation. Assurez-vous que les chemins vers ces clés sont correctement définis dans le fichier de configuration JWT (`config/packages/lexik_jwt_authentication.yaml`).

## Utilisation

L'API utilise le standard JWT pour l'authentification. Avant d'accéder aux endpoints protégés, les utilisateurs doivent s'authentifier pour obtenir un token JWT.

Toutes les routes de l'API commencent par le préfixe `/api/`.

## Tests

Pour exécuter les tests unitaires et fonctionnels, utilisez :

```bash
php bin/phpunit
```

## Support

Pour toute question ou problème, veuillez ouvrir une issue sur GitHub.
