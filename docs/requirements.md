# SpotiTube Requirements Overview

## Source Materials
- TSPP_LR_2023.pdf (methodical guidance for lab works 1-8)
- User brief (this chat) describing SpotiTube scope and quality constraints

## Mandatory Capabilities (per user brief & Appendix A of manual)
1. Secure registration, authentication, and Google OAuth onboarding.
2. Streaming playback of hosted or proxied music assets.
3. Rating workflows for artists, bands, and albums with anti-abuse controls.
4. Catalog taxonomy for artists, albums, and genres with flexible filtering.
5. Recommendation engine driven by rated tracks/albums (MVP: collaborative filtering baseline, extensible to ML later).
6. Administrative CMS for content curation, moderation, and release management.

## Process & Reporting Expectations (per manual)
- Follow structured lab phases: requirements capture, UML design, UI prototyping, architecture planning, implementation, VCS discipline, extension modules, and QA/testing.
- Produce lab artifacts: requirements spec, UML diagrams, UI mockups, architecture comparison, implementation plan, Git evidence, extension catalog, and QA checklists.
- Maintain detailed lab reports with title, goal, tasks, theoretical background summary, execution steps, and control questions coverage.
- Document each DB/ENV/API change via MIGRATION NOTE blocks.

## Engineering Guardrails (user-defined)
1. **Security first**: OWASP ASVS/Top10 compliance, zero secret leakage, structured logging with correlation IDs, no PII in logs.
2. **Code quality**: PHP >= 8.1 (target 8.4), declare(strict_types=1); PSR-12, PSR-4, PHP-CS-Fixer, PHPStan/Psalm.
3. **Architecture**: layered (Domain/Application/Adapters/Infrastructure), DI container (Laravel), prefer composition, clear contracts.
4. **Configuration**: ENV driven, validated at bootstrap, no hard-coded secrets.
5. **Operations**: rate limiting, cors allow-list, health checks, tracing, metrics.
6. **Documentation**: docs/README, CHANGELOG, MIGRATION_NOTES, OpenAPI; README kept current.
7. **Governance**: TODOs flagged with expiry, MIGRATION NOTE structure (area, change, steps, impact).
