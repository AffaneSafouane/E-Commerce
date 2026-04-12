## Look Up

Boutique e-commerce Symfony sur le theme de l'astronomie.

Look Up permet de parcourir des univers (categories), consulter les produits, gerer un panier en session, passer commande et administrer le catalogue via EasyAdmin.

## Stack Technique

- PHP 8.4+
- Symfony 8
- Doctrine ORM + Migrations
- Twig + Bootstrap 5
- EasyAdmin 5
- VichUploaderBundle (gestion des medias)
- PHPUnit

## Lancement Rapide

1. Installer les dependances:

```bash
composer install
```

2. Configurer l'environnement puis executer les migrations:

```bash
php bin/console doctrine:migrations:migrate
```

3. Lancer l'application (au choix):

```bash
symfony server:start
```

ou

```bash
php -S 127.0.0.1:8000 -t public
```

4. Lancer les tests:

```bash
bin/phpunit
```

## Fonctionnalites

### Checklist d'avancement

- [x] Login (connexion) - OK
- [x] Inscription avec un controle de majorite sur la date de naissance - OK
- [x] Parcours par categorie - OK
- [x] Parcours des articles - OK
- [x] Mise au panier - OK
- [x] Ajustement des quantites au panier avec le prix total - OK
- [x] Message de commande faite - OK
- [x] Ajout d'un nouveau type d'article propose - OK
- [x] Ajout d'une nouvelle categorie - OK
- [x] Mise a jour du profil du client connecte - OK

## Notes Projet

- Le panier est gere via la session Symfony.
- Le front suit une direction visuelle sombre, moderne et astronomique.
- L'administration (produits, categories, medias) est disponible via EasyAdmin.
