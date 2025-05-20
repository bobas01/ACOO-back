# ACOO

## Description
ACOO est une application Symfony utilisant API Platform et MySQL pour gérer des informations sur des événements et des actualités.

## Prérequis
- **Docker** : Assurez-vous d'avoir Docker et Docker Compose installés sur votre machine. Vous pouvez télécharger Docker Desktop à partir de [Docker Hub](https://www.docker.com/products/docker-desktop).
- **Composer** : Assurez-vous d'avoir Composer installé sur votre machine. Vous pouvez le télécharger à partir de [getcomposer.org](https://getcomposer.org/download/).
- **Docker Compose** : Inclus avec Docker Desktop, il vous permet de gérer des applications multi-conteneurs.

## Cloner le projet

Pour cloner le projet, exécutez la commande suivante dans votre terminal :

git clone git@github.com:bobas01/ACOO.git

## Configuration

1. Créer un fichier `.env` : Copiez le fichier `.env.example` en `.env` et modifiez les variables d'environnement si nécessaire.

2. Démarrer les conteneurs : Utilisez Docker Compose pour démarrer les conteneurs :

   docker-compose up -d

3. Installer les dépendances : Accédez au conteneur de l'application et installez les dépendances :

   docker-compose exec app composer install

4. Créer la base de données : Exécutez la commande suivante pour créer la base de données :

   docker-compose exec app php bin/console doctrine:database:create

5. Exécuter les migrations : Appliquez les migrations pour créer les tables nécessaires :

   docker-compose exec app php bin/console doctrine:migrations:migrate

## Accéder à l'application

Une fois les conteneurs en cours d'exécution, vous pouvez accéder à l'application via votre navigateur à l'adresse suivante :
http://localhost:8080

## API Platform

L'API est accessible à l'adresse suivante :

http://localhost/api

## Arrêter les conteneurs

Pour arrêter les conteneurs, exécutez :

docker-compose down

