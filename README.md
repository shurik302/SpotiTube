# SpotiTube

Laravel 12 + PHP 8.4 platform for secure music streaming, aligning with the TSPP lab methodology.

## Getting Started
1. Install dependencies: composer install (already executed during scaffold).
2. Copy .env.example to .env (already present) and update:
   - APP_URL, APP_ENV, APP_KEY (generated automatically).
   - Database block (DB_DATABASE=spotitube, DB_USERNAME=spotitube_app, set DB_PASSWORD via secret store or .env.local).
3. Run database migrations once schema is defined: php artisan migrate.
4. Start dev server: php artisan serve --host=127.0.0.1 --port=8000.

## Structure (key folders)
- pp/Domain, pp/Application, etc. (to be introduced) for clean layering.
- ootstrap/, config/, outes/, esources/, storage/ – standard Laravel runtime.
- docs/ – requirements, architecture, DB design, delivery plan, migration notes, changelog.
- prompts/ – place for stored prompt history/instructions.
- 	ests/ – PHPUnit/Pest (default examples currently present).

## Tooling
- PHP-CS-Fixer config: .php-cs-fixer.php (run endor/bin/php-cs-fixer fix).
- PHPStan config: phpstan.neon (run endor/bin/phpstan analyse).
- Laravel Pint, PHPUnit, Pest already included via Composer.

## Operational Notes
- After configuration/code changes run php artisan optimize:clear && php artisan config:cache to ensure caches are fresh.
- Keep docs/ updated (requirements, architecture, DB, delivery plan, MIGRATION_NOTES, CHANGELOG) with each iteration per TSPP instructions.

## Status
Laravel scaffold is in place; next steps include porting Symfony-specific docs to Laravel wording (partially done), defining the domain schema, and implementing authentication/streaming features.
