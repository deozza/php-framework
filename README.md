# PHP Framework

Guide pour suivre étape par étape la création d'un framework simple en PHP.

Ce framework se base sur le modèle MVC et s'inspire de ce que peuvent faire Symfony et Laravel. Il sera composé d'un routeur, d'un ORM et d'un moteur de template simple.

On pourra utiliser ce framework pour créer des API ou des applications monolithes.

## Sommaire

<!--toc:start-->
- [PHP Framework](#php-framework)
  - [Sommaire](#sommaire)
  - [Étape 1](#étape-1)
  - [Étape 2](#étape-2)
<!--toc:end-->

## Étape 0

**Objectif** : initialiser le projet

- créer un fichier `docker-compose.yml` et un fichier `Dockerfile` avec les configurations nécessaires pour développer une application en PHP
- ajouter un fichier `app/index.php` pour pouvoir tester si le conteneur est valide

## Étape 1

**Objectif** : rediriger toutes les requêtes vers `app/src/index.php`

- pour pouvoir préparer le routeur, on a besoin de rediriger toutes les requêtes vers une entrée unique
- créer un dossier `app/src` : il contiendra l'intégralité du code PHP du framework
- y déplacer le fichier `index.php`
- créer un fichier `app/.htaccess` pour y écrire les règles de redirection
