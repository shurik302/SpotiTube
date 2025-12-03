# MIGRATION_NOTES

_No migrations executed yet. Each future change must follow:_
- **Area**: DB/ENV/Docs/API
- **Summary**: short description
- **Steps**: backup > migrate > seed > verify > rollback
- **Impact**: downtime, compatibility, client changes

---

## 2025-11-25 — DB/ENV
- **Summary:** Reset legacy databases, created `spotitube` / `spotitube_test`, and provisioned dedicated MySQL user `spotitube_app`.
- **Steps Executed:** Backup (none available) → Dropped `fitness_tracker`, `mydb` → Created new DBs → Created user with scoped grants → Verified via `SHOW DATABASES`.
- **Follow-up:** Store `DB_PASSWORD` securely in `.env.local`/secret store; rotate default password before any shared deployment.
- **Impact:** No downtime (empty environment); clients must switch to new credentials.

## 2025-11-25 — Docs/Env
- **Summary:** Migrated bootstrap framework from Symfony skeleton to Laravel 12 to align with artisan workflow requirements.
- **Steps Executed:** Scaffolded Laravel via Composer, copied project files, restored docs/tooling configs, updated `.env` defaults and architecture docs.
- **Impact:** Developer workflow now uses `php artisan ...` commands; ensure local PHP has required extensions (fileinfo, pdo_mysql). Symfony-specific commands/configs are obsolete.

## 2025-11-25 — Docs/API
- **Summary:** Added base layered directories, domain/application contracts, requestId middleware, and `/api/healthz` endpoint.
- **Steps Executed:** Created Domain/Application/Infrastructure scaffolding, registered middleware stack via `bootstrap/app.php`, exposed health route/controller.
- **Impact:** All HTTP requests now include `X-Request-Id`; monitoring integrations can rely on `/api/healthz` for liveness checks.
