# Project Guidelines

## Code Style
- Target PHP 8.4+ and Symfony 8.0.
- Use strict typing (`declare(strict_types=1);`) at the top of all PHP files.
- Leverage modern PHP features: constructor property promotion, readonly properties/classes where appropriate, and match expressions.
- Follow [.editorconfig](../.editorconfig): 4-space indentation for code, 2-space indentation for compose YAML, LF line endings, trailing whitespace trimmed.
- Keep PHP aligned with the existing style in [src/Entity/Product.php](../src/Entity/Product.php), [src/Form/CustomerAddressType.php](../src/Form/CustomerAddressType.php), and [src/Controller/RegistrationController.php](../src/Controller/RegistrationController.php): attribute-based metadata, typed properties, fluent setters, and French validation messages.
- Prefer small, explicit changes over broad refactors.

## Architecture
- [src/](../src/) uses the `App\` namespace and is wired as services through [config/services.yaml](../config/services.yaml).
- Controllers use Symfony route attributes and extend `AbstractController`; route definitions are imported from [config/routes.yaml](../config/routes.yaml).
- Domain code is separated by concern: 
  - Entities in [src/Entity/](../src/Entity/)
  - Repositories in [src/Repository/](../src/Repository/)
  - Forms in [src/Form/](../src/Form/)
  - Enums in [src/Enum/](../src/Enum/)
  - Business logic/Services in [src/Service/](../src/Service/) (e.g., `CartService`)
- Back-office controllers (EasyAdmin) are located in [src/Controller/Admin/](../src/Controller/Admin/).
- Twig templates live in [templates/](../templates/); the base layout is [templates/base.html.twig](../templates/base.html.twig) and loads Bootstrap 5 from a CDN.
- Frontend assets (JS/CSS) are managed via Symfony AssetMapper.
- Security is entity-backed on `User.email`, with role protection for `/admin` (`ROLE_ADMIN`) and `/profil` (`ROLE_USER`) in [config/packages/security.yaml](../config/packages/security.yaml).

## Build and Test
- Install dependencies with `composer install`.
- Database workflow: generate migrations with `php bin/console make:migration` and apply them with `php bin/console doctrine:migrations:migrate`.
- Use `php bin/console` for Symfony commands; common ones here are `debug:router`, `debug:autowiring`, and `cache:clear`.
- Run the test suite with `bin/phpunit`.
- Composer post-install and post-update scripts run cache clear, asset install, and importmap install automatically.
- PHPUnit is configured to fail on deprecations, notices, and warnings; keep test output clean.

## Conventions
- Prefer `app_` route names and class-level route prefixes when it improves organization.
- Bind forms directly to entities via `data_class`, and keep validation close to the form type.
- **State Management:** The shopping cart logic MUST be managed via the Symfony Session (using `RequestStack`), NOT persisted in the database.
- When adding related entities from forms, verify persistence explicitly (`$entityManager->persist()`) instead of assuming nested objects are automatically saved (unless `cascade` is configured).
- Link to existing config and reference files such as [config/reference.php](../config/reference.php) when documenting framework behavior instead of duplicating it.
- Avoid editing generated or vendor-managed files such as [assets/vendor/installed.php](../assets/vendor/installed.php).