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

### Focus Technique : Gestion Hybride des Médias

Le projet implémente une logique de rendu flexible pour les ressources multimédias (`Media`), permettant de concilier environnement de démonstration et utilisation réelle :

1. **En production / utilisation réelle** : Les médias sont gérés via `VichUploaderBundle`. Les fichiers sont stockés localement dans `public/uploads/media/` et le nom du fichier est persisté en base de données.
2. **En environnement de test / démo** : Afin de proposer un catalogue riche dès l'installation, les **Fixtures (Zenstruck Foundry)** génèrent des URLs distantes (via Faker et Picsum Photos).

**Implémentation :**
Une logique de détection a été intégrée dans l'entité `Media` ainsi que dans les templates Twig via la fonction `str_starts_with` (ou `starts with` en Twig). 
- Si le chemin commence par `http`, le système affiche l'URL directe.
- Sinon, il utilise le helper `asset()` pour pointer vers le répertoire d'upload local.

Cette approche permet au correcteur de bénéficier d'un catalogue illustré immédiatement après le chargement des fixtures, tout en conservant un système d'upload fonctionnel dans l'administration.

## Lancement Rapide

1. Installer les dependances:

```bash
composer install
```

2. Configurer l'environnement puis executer les migrations:

```bash
php bin/console doctrine:migrations:migrate
```

3. Charger les données de test (Produits, Catégories, Utilisateurs) :
```bash
php bin/console doctrine:fixtures:load
```

4. Lancer l'application (au choix):

```bash
symfony server:start
```

ou

```bash
php -S 127.0.0.1:8000 -t public
```

5. Lancer les tests:

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

## Comptes de Test
Le projet utilise **Zenstruck Foundry** pour générer un catalogue complet. Une fois les fixtures chargées (`php bin/console doctrine:fixtures:load`), vous pouvez tester l'application avec les comptes suivants :

| Rôle | Email | Mot de passe |
| :--- | :--- | :--- |
| **Administrateur** | `admin@lookup.fr` | `admin123` |
| **Utilisateur Client** | `user@lookup.fr` | `user123` |

*Note : Les 10 autres utilisateurs générés aléatoirement possèdent tous le mot de passe `password123`.*

## Site Hébérger
Le site web est hébérger sur Alwaysdata à l'url suivante : 
[E-Commerce](https://saffane.alwaysdata.net/e-commerce/)