# Architecture Overview

## Stack
- Laravel 12.x (artisan tooling, service container, middleware pipeline)
- PHP 8.4 with strict typing, PSR-12, PHPStan/Pint
- Database: MySQL 8 (primary) with optional Redis for caching/queues
- HTTP delivery via Laravel controllers/API resources; client SPA can integrate later

## Layering
```
app/
  Domain/           # aggregates, entities, value objects, policies
  Application/      # use-cases, DTOs, service interfaces
  Infrastructure/
    Http/           # controllers, requests, presenters
    Persistence/    # Eloquent models, repositories, migrations
```
`routes/` defines entry points; `config/` wires services/ENV validation.

## Security & Observability
- Laravel Sanctum/Passport for token auth + built-in CSRF/session protection
- RateLimiter facade for per-user/IP throttling, middleware for CORS
- Monolog channel with JSON formatter + requestId propagation middleware
- Health-check endpoint `/healthz` plus `artisan health:check` (custom)

## External Integrations
- Google OAuth via Socialite or custom provider
- Streaming/CDN connector abstracted behind domain port (e.g., S3, CloudFront)
- Recommendation engine injected via interface; initial rule-based, later ML
